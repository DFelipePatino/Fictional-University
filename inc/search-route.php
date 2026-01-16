<?php

add_action('rest_api_init', 'universityRegisterSearch');

// function universityRegisterSearch()
// {
//   register_rest_route('university/v1', 'credits', array(
//     'methods' => WP_REST_SERVER::READABLE,
//     'callback' => 'universitySearchResults'
//   ));
// }


function universityRegisterSearch()
{
  register_rest_route('university/v1', 'search', array(
    'methods' => WP_REST_SERVER::READABLE,
    'callback' => 'universitySearchResults'
  ));
}


// function universitySearchResults()
// {
//   return array(
//     "creditType" => "Website Development Credit",
//     "name" => "Daniel P",
//     "role" => "Full Stack Developer, Web Developer",
//     "project" => array(
//       "type" => "Website",
//       "description" => "Design and development of a complete website",
//       "technologiesUsed" => array(
//         "HTML",
//         "CSS",
//         "JavaScript",
//         "PHP",
//         "REST API"
//       )
//     ),
//     "creditStatement" => "This website was designed and developed by Daniel P.",
//     "date" => array(
//       "issued" => "2026-01-15",
//       "format" => "YYYY-MM-DD"
//     ),
//     "ownership" => array(
//       "author" => "Daniel P",
//       "rights" => "All development credit attributed to the author unless otherwise stated"
//     ),
//     "verification" => array(
//       "selfCertified" => true,
//       "generatedBy" => "Custom JSON Credit Generator"
//     )
//   );
// }


function universitySearchResults($data)
{
  $mainQuery = new WP_Query(array(
    'post_type' => array('post', 'page', 'professor', 'program', 'campus', 'event'),
    's' => sanitize_text_field($data['term'])
  ));

  $results = array(
    'generalInfo' => array(),
    'professors' => array(),
    'programs' => array(),
    'events' => array(),
    'campuses' => array()
  );

  while ($mainQuery->have_posts()) {
    $mainQuery->the_post();

    if (get_post_type() == 'post' or get_post_type() == 'page') {
      array_push($results['generalInfo'], array(
        'title' => get_the_title(),
        'permalink' => get_the_permalink()
      ));
    }

    if (get_post_type() == 'professor') {
      array_push($results['professors'], array(
        'title' => get_the_title(),
        'permalink' => get_the_permalink()
      ));
    }

    if (get_post_type() == 'program') {
      array_push($results['programs'], array(
        'title' => get_the_title(),
        'permalink' => get_the_permalink()
      ));
    }

    if (get_post_type() == 'campus') {
      array_push($results['campuses'], array(
        'title' => get_the_title(),
        'permalink' => get_the_permalink()
      ));
    }

    if (get_post_type() == 'event') {
      array_push($results['events'], array(
        'title' => get_the_title(),
        'permalink' => get_the_permalink()
      ));
    }
  }

  return $results;
}
