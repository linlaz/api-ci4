<?php

namespace App\Controllers;

use App\Models\Category;
use App\Traits\ResponseAllow;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class CategoriesController extends ResourceController
{
    use ResponseTrait;
    USE ResponseAllow;

    public $categories;
    public function __construct() {
        $this->categories = new Category();
    }
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        $status =$this->request->getVar('status');
        if ($status !== null && $status !== '') {
            $data = $this->categories->where('status',$status)->findAll();
        }else{
            $data = $this->categories->findAll();
        }
        $response = [
			'status' => 200,
			"error" => false,
			'messages' => 'categories list',
			'data' => $data
		];
        return $this->responseAllow($response);
    }

    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function show($id = null)
    {
        $data = $this->categories->find($id);
        if (!empty($data)) {

			$response = [
				'status' => 200,
				"error" => false,
				'messages' => 'categories finded successfully',
				'data' => $data
			];

		}else{
            $response = [
				'status' => 404,
				"error" => true,
				'messages' => 'No categories found',
				'data' => []
			];
        }
        return $this->responseAllow($response);
    }

    /**
     * Create a new resource object, from "posted" parameters
     *
     * @return mixed
     */
    public function create()
    {
        $valid = $this->validate([
            'name' => 'required',
            'status' => 'required|statusMustPublishedOrDraft'
        ],[
            'status'=> [
                'statusMustPublishedOrDraft' => 'format status false'
            ]
        ]);
        if (!$valid) {
            $response = [
                'status' => 400,
                'error' => true,
                'message' => $this->validator->getErrors(),
                'data' => []
            ];
        } else {
            $data = [
                'name' => $this->request->getVar('name'),
                'status' => $this->request->getVar('status'),
            ];
            $this->categories->insert($data);
            $response = [
				'status' => 200,
				'error' => false,
				'message' => 'category added successfully',
				'data' => $data
			];
        }
       
        return $this->responseAllow($response);
    }

    /**
     * Add or update a model resource, from "posted" properties
     *
     * @return mixed
     */
    public function update($id = null)
    {
        $rules = [
            'name' => 'required',
            'status' => 'required'
        ];   
        if (!$this->validate($rules)) {
			$response = [
				'status' => 500,
				'error' => true,
				'message' => $this->validator->getErrors(),
				'data' => []
			];
		} else { 
            $datas = $this->categories->find($id);
            if (!empty($datas)) {
                $data = [
                    'name' => $this->request->getVar('name'),
                    'status' => $this->request->getVar('status'),
                ];
                $this->categories->update($id,$data);
                $response = [
					'status' => 200,
					'error' => false,
					'message' => 'category updated successfully',
					'data' => $this->categories->find($id)
				];
            }else{
                $response = [
					'status' => 404,
					"error" => true,
					'messages' => 'No category found',
					'data' => []
				];
            }
         }
         return $this->responseAllow($response);
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        $data = $this->categories->find($id);
        if (!empty($data)) {
            $this->categories->delete($id);
            $response = [
                'status'   => 200,
                'messages' => [
                    'success' => 'category successfully deleted'
                ],
                'data' => []
            ];
            
        } else {
            $response = [
				'status' => 404,
				"error" => true,
				'messages' => 'No category found',
				'data' => []
			];
        }
        return $this->responseAllow($response);
    }
}
