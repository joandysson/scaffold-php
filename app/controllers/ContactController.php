<?php

namespace App\Controllers;

use Services\Email;
use Config\Controller\CoreController;

class ContactController extends CoreController
{
  public function test($data)
  {
    $template = view('contact', ['data' => $data], true);
    $result = Email::send(
      explode(',', getEnvelopment('EMAIL', 'EMAIL')),
      'test',
      'test',
      $template
    );

    if ($result) {
      echo 'success';
      return;
    }

    echo 'error' . $result;
    return;
  }
  function create()
  {
    return view('contact');
  }
}
