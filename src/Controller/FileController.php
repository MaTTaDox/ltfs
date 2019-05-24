<?php

namespace App\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;

class FileController extends BaseController
{
    /**
     * @Rest\Route("api/files", name="get_files")
     */
    public function getUserAction()
    {
        return $this->renderRest([]);
    }
}
