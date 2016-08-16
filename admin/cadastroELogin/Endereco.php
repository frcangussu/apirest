<?php

  class Endereco {

    var $quadra;
    var $rua;
    var $complemento;

    function __construct(){

    }

    function getEndereco(){
      
    }

    function getQuadra(){
      return $this->quadra;
    }

    function setQuadra($quadra){
      $this->quadra = $quadra;
    }

    function getRua(){
      return $this->rua;
    }

    function setRua($rua){
      $this->rua = $rua;
    }

    function getComplemento(){
      return $this->complemento;
    }

    function setComplemento($complemento){
      $this->complemento = $complemento
    }


  }

 ?>
