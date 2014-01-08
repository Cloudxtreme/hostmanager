<?php

/*
 * Developer : pan-x (info@pan-x.com)
 * All code (c)2013 pan-x all rights reserved
 */

Class clientModel extends CI_Model
{

    // Name der Tabelle
    private $tbl_name= 'client';

    function __construct(){
        parent::__construct();
    }

    function count_all(){
        return $this->db->count_all($this->tbl_name);
    }

    function get_paged_list($limit = 10, $offset = 0){
        $this->db->order_by('client_name','asc');
        return $this->db->get($this->tbl_name, $limit, $offset);
    }

    function get_by_id($client_id){
        $this->db->where('client_id', $client_id);
        return $this->db->get($this->tbl_name);
    }

    function load_session_by_url($client_url){
        $this->db->where('client_url', $client_url);
        $result = $this->db->get($this->tbl_name);
        foreach ( $result->result() as $row ) {
            $_SESSION['client']['client_id'] = $row->client_id;
            $_SESSION['client']['client_name'] = $row->client_name;
            $_SESSION['client']['client_url'] = $row->client_url;
            $_SESSION['client']['client_mail'] = $row->client_mail;
        }
    }

    function load_session_by_id($client_id){
        $this->db->where('client_id', $client_id);
        $result = $this->db->get($this->tbl_name);
        foreach ( $result->result() as $row ) {
            $_SESSION['client']['client_id'] = $row->client_id;
            $_SESSION['client']['client_name'] = $row->client_name;
            $_SESSION['client']['client_url'] = $row->client_url;
            $_SESSION['client']['client_mail'] = $row->client_mail;
        }
    }

    function save($client){
        $this->db->insert($this->tbl_name, $client);
        return $this->db->insert_id();
    }

    function update($client_id, $client){
        $this->db->where('client_id', $client_id);
        $this->db->update($this->tbl_name, $client);
    }

    function delete($client_id){
        $this->db->where('client_id', $client_id);
        $this->db->delete($this->tbl_name);
    }



}
?>
