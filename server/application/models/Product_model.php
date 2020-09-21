<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Product_model extends CI_Model
{
    public function get_product_all()
    {
        $product = $this->db->query("SELECT * FROM vw_products")->return_array();
        if ($product) {
            for ($i = 0; $i < count($product); $i++) {
                $product[$i]['product_pictures'] = explode(", ", $product[$i]['product_pictures']);
            }
            return $product;
        } else {
            return false;
        }
    }

    public function get_product_by_id($id)
    {
        $product = $this->db->query("SELECT * FROM vw_products WHERE id = $id")->row_array();
        if ($product) {
            $product['product_pictures'] = explode(", ", $product['product_pictures']);
            return $product;
        } else {
            return false;
        }
    }

    public function get_product_by_name($name)
    {
        $product = $this->db->query("SELECT * FROM vw_products WHERE product_name LIKE '%$name%'")->return_array();
        if ($product) {
            for ($i = 0; $i < count($product); $i++) {
                $product[$i]['product_pictures'] = explode(", ", $product[$i]['product_pictures']);
            }
            return $product;
        } else {
            return false;
        }
    }

    public function get_product_by_category_name($category_name)
    {
        $product = $this->db->query("SELECT * FROM vw_products category_name LIKE '%$category_name%'")->return_array();
        if ($product) {
            for ($i = 0; $i < count($product); $i++) {
                $product[$i]['product_pictures'] = explode(", ", $product[$i]['product_pictures']);
            }
            return $product;
        } else {
            return false;
        }
    }

    public function add_product($product_data)
    {
        $name = $product_data['name'];
        $description = $product_data['description'];
        $stock = $product_data['stock'];
        $category_id = $product_data['category_id'];
        $price = $product_data['price'];
        $pictures = $product_data['pictures'];

        $this->db->trans_begin();

        $uuid = $this->db->query("SELECT uuid_short()")->row_array()["uuid_short()"];

        $query_insert = "INSERT INTO products(id, product_id, product_name, product_description, product_stock, produck_avaibility, product_price, product_category) VALUES ($uuid, product_id(), '$name', '$description', $stock, $stock, $price, $category_id)";
        if (!$this->db->simple_query($query_insert)) {
            $error = $this->db->error();
            $this->db->trans_rollback();
            return $error;
        } else {
            for ($i = 0; $i < count($pictures); $i++) {
                $query_insert_picture = "INSERT INTO product_pictures(product_id, picture) VALUES($uuid, '$pictures[$i]')";
                if (!$this->db->simple_query($query_insert_picture)) {
                    $error = $this->db->error();
                    $this->db->trans_rollback();
                    return $error;
                }
            }
            $affected_row = $this->db->affected_rows();
            $this->db->trans_commit();
            return $affected_row;
        }
    }

    public function delete_product($id_product)
    {
        $query_delete = "DELETE FROM products WHERE id = $id_product";
        $this->db->trans_begin();
        if (!$this->db->simple_query($query_delete)) {
            $error = $this->db->error();
            $this->db->trans_rollback();
            return $error;
        } else {
            $affected_row = $this->db->affected_rows();
            $this->db->trans_commit();
            return $affected_row;
        }
    }

    public function update_product($product_data)
    {
        $id = $product_data['id'];
        $name = $product_data['name'];
        $description = $product_data['description'];
        $stock = $product_data['stock'];
        $category_id = $product_data['category_id'];
        $price = $product_data['price'];
        $pictures = $product_data['pictures'];

        $this->db->trans_begin();
        $query_delete_picture = "DELETE FROM product_pictures WHERE product_id = $id";
        if (!$this->db->simple_query($query_delete_picture)) {
            $error = $this->db->error();
            $this->db->trans_rollback();
            return $error;
        } else {
            $query_update = "UPDATE products SET product_name = '$name', product_description = '$description', product_stock = '$stock', product_price = '$price', product_category = $category_id WHERE id = $id";
            if (!$this->db->simple_query($query_update)) {
                $error = $this->db->error();
                $this->db->trans_rollback();
                return $error;
            } else {
                for ($i = 0; $i < count($pictures); $i++) {
                    $query_insert_picture = "INSERT INTO product_pictures(product_id, picture) VALUES($id, '$pictures[$i]')";
                    if (!$this->db->simple_query($query_insert_picture)) {
                        $error = $this->db->error();
                        $this->db->trans_rollback();
                        return $error;
                    }
                }
                $affected_row = $this->db->affected_rows();
                $this->db->trans_commit();
                return $affected_row;
            }
        }
    }
}