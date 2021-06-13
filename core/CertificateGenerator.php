<?php
require_once PLUGIN_PATH . '/libs/vendor/autoload.php';

class CertificateGenerator
{
    const VIEW = 'view';
    const DOWNLOAD = 'download';
    public $template = null;
    public $data = null;

    public function __construct(CertificateTemplate $template, $data = null)
    {
        $this->template = $template;
        $this->data = $data;
    }

    public function render($filename = 'certificate.pdf', $type = self::VIEW)
    {
        $fields = $this->template->getFields();
        if (!is_null($this->data)){
            foreach ($fields as $field){
                switch ($field->code){
                    case 'name':
                        $field->example_text = $this->data['name'];
                        break;
                    case 'date':
                        #$field->example_text = $this->data['date'];
                        $field->example_text = date('d-m-Y', strtotime($this->data['date']));
                        break;
                    case 'date_end':
                        $field->example_text = date('d-m-Y', strtotime($this->data['date_end']));
                        break;
                    case 'series':
                        $field->example_text = $this->data['series'];
                        break;
                    case 'number':
                        $field->example_text = $this->data['number'];
                        break;
                    case 'course':
                        $field->example_text = $this->data['course'];
                        break;
                    case 'field1':
                        $field->example_text = $this->data['field1'];
                        break;
                    case 'field2':
                        $field->example_text = $this->data['field2'];
                        break;
                }
            }
        }
        $image_src = $this->template->getImgSrc();
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
            if ($type === self::VIEW) {
                $mpdf->Output($filename, \Mpdf\Output\Destination::INLINE);
            } elseif ($type === self::DOWNLOAD) {
                $mpdf->Output($filename, \Mpdf\Output\Destination::DOWNLOAD);
            }
            die();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function getImage(string $filename): string
    {
        ob_start();
        ?>
        <div style="position: absolute; z-index: -1; left:0; right: 0; top: 0; bottom: 0;">
            <img src="<?= $filename; ?>"
                 style="width: 210mm; height: 297mm; margin: 0;"/>
        </div>
        <?php
        return ob_get_clean();
    }

    public function getField(array $props): string
    {
        $values = [];
        foreach ($props as $key => $value) {
            if ($key === 'position') {
                $values[] = "top:{$value->top}px;";
                $values[] = "left:{$value->left}px;";
                continue;
            } elseif ($key === 'size') {
                $values[] = "width:{$value->width}px;";
                continue;
            } elseif ($key === 'text' || $key === 'title' || $key === 'example_text') {
                continue;
            }
            $px = is_numeric($value) ? 'px' : '';
            $values[] = !empty($value)
                ? preg_replace("|_|", "-", $key) . ": {$props[$key]}$px;"
                : '';
        }

        ob_start();
        ?>
        <div style="
                display: inline;
                position: absolute;
                height: auto;
        <?php foreach ($values as $value): ?>
            <?= $value ?>
        <?php endforeach; ?>
                ">
            <?= $props['example_text'] ?>
        </div>
        <?php
        return ob_get_clean();
    }

    public static function getCertificateGeneratorByCertificate(Certificate $certificate): CertificateGenerator
    {
        return new CertificateGenerator(
            CertificateTemplate::getTemplate($certificate->certificate_template_id),
            [
                'name' => getFIO($certificate->user_id),
                'date' => $certificate->date_issue,
                'date_end' => $certificate->date_end,
                'series' => $certificate->series,
                'number' => $certificate->number,
                'course' => $certificate->course_name,
                'field1' => $certificate->getAdditionFields()['field1'],
                'field2' => $certificate->getAdditionFields()['field2'],
            ]
        );
    }
}
