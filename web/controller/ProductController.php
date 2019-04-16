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

    /**
     * 添加/编辑产品
     * @return mixed
     */
    public function edit()
    {
        try {
            $product_name = $this->request->get("product_name");
            $product_num = $this->request->get("product_num");
            $product_id = $this->request->get("product_id");
            $sProduct = new ProductService();
            $result = $sProduct->editProduct($product_name, $product_num, $this->uid, $product_id);
            if (true === $result) {
                return $this->response->json(200, self::$success, ["product_name" => $product_name, "product_num" => $product_num]);
            } else {
                return $this->response->json(500, self::$error, "");
            }
        } catch (\Exception $e) {
            return $this->response->json(500, self::$error, $e->getMessage());
        }
    }

    /**
     * 获取产品
     * @return mixed
     * @throws \Exception
     */
    public function getProduct()
    {
        $page = $this->request->get("page");
        $limit = $this->request->get("limit");
        $uid = $this->request->get("uid");
        $sProduct = new ProductService();
        $products = $sProduct->getProduct($page, $limit, $uid);
        return $this->response->json(200, self::$success, $products);
    }

}