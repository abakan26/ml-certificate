<?php
require_once PLUGIN_PATH . '/libs/vendor/autoload.php';

/**
 * Class CertificateGeneratorJPG
 * Decorator for class CertificateGenerator
 */
class CertificateGeneratorJPG
{
    private $origin;

    public function __construct(CertificateGenerator $generator)
    {
        $this->origin = $generator;
    }

    public function render($filename = 'certificate.jpg', $type = CertificateGenerator::VIEW)
    {
        $fields = $this->origin->template->getFields();
        if (!is_null($this->origin->data)){
            foreach ($fields as $field){
                switch ($field->code){
                    case 'name':
                        $field->example_text = $this->origin->data['name'];
                        break;
                    case 'date':
                        #$field->example_text = $this->data['date'];
                        $field->example_text = date('d-m-Y', strtotime($this->origin->data['date']));
                        break;
                    case 'date_end':
                        $field->example_text = date('d-m-Y', strtotime($this->origin->data['date_end']));
                        break;
                    case 'series':
                        $field->example_text = $this->origin->data['series'];
                        break;
                    case 'number':
                        $field->example_text = $this->origin->data['number'];
                        break;
                    case 'course':
                        $field->example_text = $this->origin->data['course'];
                        break;
                    case 'field1':
                        $field->example_text = $this->origin->data['field1'];
                        break;
                    case 'field2':
                        $field->example_text = $this->origin->data['field2'];
                        break;
                }
            }
        }
        $image_src = $this->origin->template->getImgSrc();
        $defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
        $defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => [210, 297],
            'fontDir' => array_merge($fontDirs, FontHandler::getDirs()),
            'dpi' => 96,
            'fontdata' => $fontData + FontHandler::getFonts(),
            'default_font' => FontHandler::getDefault()
        ]);
        /***/
        $pdf_path = tempnam(dirname(__DIR__) . '/temp', '');
        $jpg_path = tempnam(dirname(__DIR__) . '/temp', '');
        rename($pdf_path, $pdf_path . '.pdf');
        rename($jpg_path, $jpg_path . '.jpg');
        $pdf_file = $pdf_path . '.pdf';
        $jpg_file = $jpg_path . '.jpg';
        /***/
        ob_clean();
        try {
            $stylesheet = file_get_contents(PLUGIN_ADMIN_PATH . '/css/fonts.css');
            $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);
            $mpdf->BeginLayer(1);
            $mpdf->WriteHTML($this->getImage($image_src), \Mpdf\HTMLParserMode::HTML_BODY);
            $mpdf->EndLayer();
            $mpdf->BeginLayer(2);
            foreach ($fields as $field) {
                if ($field->hide){
                    continue;
                }
                $mpdf->WriteHTML($this->getField((array)$field));
            }
            $mpdf->Output($pdf_file, \Mpdf\Output\Destination::FILE);
            $this->convert_pdf_to_jpg($pdf_file, $jpg_file);
            $extension = pathinfo($jpg_file, PATHINFO_EXTENSION);
            $data = file_get_contents($jpg_file);
            $base64 = 'data:image/' . $extension . ';base64,' . base64_encode($data);
            if ($type === CertificateGenerator::VIEW) {
                echo "<img style='max-width: 100%' src='" . $base64 . "' />";
            } elseif ($type === CertificateGenerator::DOWNLOAD) {
                header("Content-Disposition: attachment; filename=\"".basename($jpg_file)."\";" );
                header('Content-Transfer-Encoding: binary');
                header('Accept-Ranges: bytes');
                header("Content-Length: ".filesize($jpg_file));
                header("Content-Type: image/jpg");
                echo $data;
            }
        } catch (Exception $e) {
            die($e->getMessage());
        } finally {
            unlink($pdf_file);
            unlink($jpg_file);
        }
        die();
    }

    public function getImage(string $filename): string
    {
        return $this->origin->getImage($filename);
    }

    public function getField(array $props): string
    {
        return $this->origin->getField($props);
    }

    function convert_pdf_to_jpg($pdfFile, $fileName)
    {
        $cmd = 'echo $(( $(lscpu | awk \'/^Socket/{ print $2 }\') * $(lscpu | awk \'/^Core/{ print $4 }\') ))';
        exec($cmd, $output, $exitCode);
        $processorCores = (int)$output[0];
        $cmd = 'gs -dNOPAUSE -sDEVICE=jpeg -dFirstPage=1 -dJPEGQ=100 -r300'
            . ' -dNumRenderingThreads=' . $processorCores
            . ' -dLastPage=1'
            . ' -sOutputFile=' . escapeshellarg($fileName)
            . ' -q ' . escapeshellarg($pdfFile)
            . ' -c quit';
        exec($cmd, $outputLineList, $exitCode);
        return $exitCode;
    }
}



