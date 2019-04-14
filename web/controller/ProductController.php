<?php
/**
 * Created by PhpStorm.
 * User: caijiang
 * Date: 2019/4/10
 * Time: 下午7:41
 */
namespace web\controller;


use web\service\ProductService;

class ProductController extends BaseController
{

    public function edit()
    {
        try{
            $product_name = $this->request->get("product_name");
            $product_num = $this->request->get("product_num");
            $sProduct = new ProductService();
            $result = $sProduct->editProduct($product_name, $product_num, $this->token);
            if (true === $result) {
                return $this->response->json(200, self::$success, ["product_name" => $product_name, "product_num" => $product_num]);
            } else {
                return $this->response->json(500, self::$error, "");
            }
        }catch (\Exception $e){
            return $this->response->json(500, self::$error, $e->getMessage());
        }
    }


}