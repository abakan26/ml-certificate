<?php
/**
 * @global int $certificate_template_id
 */


$template = CertificateTemplate::getTemplate($certificate_template_id);
$generator = new CertificateGenerator($template);
$generator->render();