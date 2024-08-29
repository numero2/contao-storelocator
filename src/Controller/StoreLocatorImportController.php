<?php

/**
 * StoreLocator Bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL-3.0-or-later
 * @copyright Copyright (c) 2024, numero2 - Agentur für digitales Marketing GbR
 */


namespace numero2\StoreLocatorBundle\Controller;

use Contao\BackendTemplate;
use Contao\Config;
use Contao\CoreBundle\Controller\BackendCsvImportController;
use Contao\CoreBundle\Exception\InternalServerErrorException;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\File;
use Contao\FileUpload;
use Contao\Input;
use Contao\Message;
use Exception;
use numero2\StoreLocator\StoresModel;
use numero2\StoreLocatorBundle\Event\StoreImportEvent;
use numero2\StoreLocatorBundle\Event\StoreLocatorEvents;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;


class StoreLocatorImportController {


    /**
     * @var string
     */
    private string $projectDir;

    /**
     * @var Symfony\Component\HttpFoundation\RequestStack
     */
    private RequestStack $requestStack;

    /**
     * @var Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private EventDispatcherInterface $eventDispatcher;

    /**
     * @var Contao\CoreBundle\Framework\ContaoFramework
     */
    private ContaoFramework $framework;

    /**
     * @var Psr\Log\LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var Symfony\Contracts\Translation\TranslatorInterface
     */
    private TranslatorInterface $translator;


    public function __construct( string $projectDir, RequestStack $requestStack, EventDispatcherInterface $eventDispatcher, ContaoFramework $framework, LoggerInterface $logger, TranslatorInterface $translator ) {

        $this->projectDir = $projectDir;
        $this->requestStack = $requestStack;
        $this->eventDispatcher = $eventDispatcher;
        $this->framework = $framework;
        $this->logger = $logger;
        $this->translator = $translator;
    }


    public function importStoreAction(): Response {

        $request = $this->requestStack->getCurrentRequest();

        if( $request === null ) {
            throw new InternalServerErrorException('No request object given.');
        }

        $this->framework->initialize();

        $uploader = $this->framework->createInstance(FileUpload::class);
        $template = $this->prepareTemplate($request, $uploader);
        // change some template data
        $template->sourceHelp = $this->translator->trans('tl_storelocator.import.file_content', [], 'contao_default');
        $template->sourceNote = $this->translator->trans('tl_storelocator.import.limit_info', [], 'contao_default');

        $data = $this->getDataIfSubmitted($uploader, $request);

        if( $data !== null ) {

            $pid = Input::get('id');
            $columns = null;
            $hasError = false;

            foreach( $data as $row ) {

                if( $columns === null ) {
                    $columns = $row;
                    continue;
                }

                if( count($columns) > count($row) ) {
                    $row = $row + array_fill(count($row), count($columns)-count($row), null);
                } else if( count($columns) < count($row) ) {
                    $row = array_slice($row, 0, count($columns));
                }

                $store = array_combine($columns, $row);

                $model = StoresModel::findOneById($store['id'] ?? 0);

                if( !$model ) {
                    $model = new StoresModel();
                }

                $model->tstamp = time();
                $model->pid = $pid;

                foreach( $store as $k => $v ) {
                    if( $v === null ) {
                        continue;
                    }
                    $model->{$k} = $v;
                }

                // parse data
                $event = new StoreImportEvent($model, $columns, $row);
                $this->eventDispatcher->dispatch($event, StoreLocatorEvents::STORE_IMPORT);

                if( !$event->getSkipImport() ) {

                    $model = $event->getModel();
                    try {
                        $model->save();
                    } catch ( Exception $e ) {
                        $hasError = true;
                        $this->logger->error("Error during importing store (Column 0: \"$row[0]\"): ". $e->getMessage());
                    }
                }
            }

            if( $hasError ) {
                $message = $this->framework->getAdapter(Message::class);
                $message->addError($this->translator->trans('tl_storelocator.import.error_see_log', [], 'contao_default'));
            }

            return new RedirectResponse($this->getBackUrl($request));
        }

        return new Response($template->parse());
    }


    private function getDataIfSubmitted( FileUpload $uploader, Request $request ): array|null {

        if ($request->request->get('FORM_SUBMIT') === $this->getFormId($request) ) {
            try {
                $data = $this->fetchData($uploader, (string) $request->request->get('separator', ''));
            } catch ( RuntimeException $e ) {
                $message = $this->framework->getAdapter(Message::class);
                $message->addError($e->getMessage());

                return new RedirectResponse($request->getUri());
            }

            return $data;
        }

        return null;
    }


    /*
     * below mostly taken from contao/contao file Contao\CoreBundle\Controller\BackendCsvImportController
     */
    private function fetchData( FileUpload $uploader, string $separator ): array {

        $data = [];
        $files = $this->getFiles($uploader);
        $delimiter = $this->getDelimiter($separator);
        ini_set('auto_detect_line_endings', true);

        foreach( $files as $file ) {
            $fp = fopen($file, 'r');

            while( false !== ($row = fgetcsv($fp, 0, $delimiter)) ) {
                $data[] = $row;
            }
        }

        return $data;
    }

    private function prepareTemplate( Request $request, FileUpload $uploader, bool $allowLinebreak=false ): BackendTemplate {

        $template = new BackendTemplate('be_csv_import_storelocator');
        $config = $this->framework->getAdapter(Config::class);

        $template->formId = $this->getFormId($request);
        $template->backUrl = $this->getBackUrl($request);
        $template->fileMaxSize = $config->get('maxFileSize');
        $template->uploader = $uploader->generateMarkup();
        $template->separators = $this->getSeparators($allowLinebreak);
        $template->submitLabel = $this->translator->trans('tl_storelocator.import.start', [], 'contao_default');
        $template->backBT = $this->translator->trans('MSC.backBT', [], 'contao_default');
        $template->backBTTitle = $this->translator->trans('MSC.backBTTitle', [], 'contao_default');
        $template->separatorLabel = $this->translator->trans('MSC.separator.0', [], 'contao_default');
        $template->separatorHelp = $this->translator->trans('MSC.separator.1', [], 'contao_default');
        $template->sourceLabel = $this->translator->trans('MSC.source.0', [], 'contao_default');
        $template->sourceHelp = $this->translator->trans('MSC.source.1', [], 'contao_default');

        return $template;
    }

    private function getFormId( Request $request ): string {

        return 'tl_csv_import_'.$request->query->get('key');
    }

    private function getBackUrl( Request $request ): string {

        return str_replace('&key='.$request->query->get('key'), '', $request->getRequestUri());
    }

    private function getSeparators( bool $allowLinebreak=false ): array {

        $separators = [
            BackendCsvImportController::SEPARATOR_COMMA => [
                'delimiter' => ',',
                'value' => BackendCsvImportController::SEPARATOR_COMMA,
                'label' => $this->translator->trans('MSC.comma', [], 'contao_default'),
            ],
            BackendCsvImportController::SEPARATOR_SEMICOLON => [
                'delimiter' => ';',
                'value' => BackendCsvImportController::SEPARATOR_SEMICOLON,
                'label' => $this->translator->trans('MSC.semicolon', [], 'contao_default'),
            ],
            BackendCsvImportController::SEPARATOR_TABULATOR => [
                'delimiter' => "\t",
                'value' => BackendCsvImportController::SEPARATOR_TABULATOR,
                'label' => $this->translator->trans('MSC.tabulator', [], 'contao_default'),
            ],
        ];

        if ($allowLinebreak) {
            $separators[BackendCsvImportController::SEPARATOR_LINEBREAK] = [
                'delimiter' => "\n",
                'value' => BackendCsvImportController::SEPARATOR_LINEBREAK,
                'label' => $this->translator->trans('MSC.linebreak', [], 'contao_default'),
            ];
        }

        return $separators;
    }

    private function getDelimiter( string $separator ): string {

        $separators = $this->getSeparators(true);

        if (!isset($separators[$separator])) {
            throw new RuntimeException($this->translator->trans('MSC.separator.1', [], 'contao_default'));
        }

        return $separators[$separator]['delimiter'];
    }

    private function getFiles( FileUpload $uploader ): array {

        $files = $uploader->uploadTo('system/tmp');

        if( count($files) < 1 ) {
            throw new RuntimeException($this->translator->trans('ERR.all_fields', [], 'contao_default'));
        }

        foreach( $files as &$file ) {
            $extension = Path::getExtension($file, true);

            if( $extension !== 'csv' ) {
                throw new RuntimeException(sprintf($this->translator->trans('ERR.filetype', [], 'contao_default'), $extension));
            }

            $file = Path::join($this->projectDir, $file);
        }

        return $files;
    }
}
