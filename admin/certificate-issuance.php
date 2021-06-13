<?php
$user = get_user_by('ID', get_current_user_id());
$isCoach = in_array('coach', $user ->roles);
$categoryOptions = Course::getProductCategory();
if ($isCoach) {
    $coach = new Coach($user->ID);
    $courseOptions = $coach->getRelatedCourses();
}

include 'templates/certificate-issuance.php';
