<?php
/**
  * Created with love using Mariana Framework
  * Need help? Ask Pihh The Creator pihh.rocks@gmail.com
  */

  # Table Name
  $table = 'comments';

  # Table Fields
  $fields = array(
        'id'              =>  'INTEGER PRIMARY KEY',
        'date_created'    =>  'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
        'last_updated'    =>  'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        'user_id'         =>  'INTEGER',
        'content'         =>  'TEXT'
  );

  # Table Seeds
  # Put here info on how you want to populate the database:
  /*
   * Example:
   *  $i = 0;
   *  while($i < 50){
   *    array_push($seeds,array('date_created'=> time(), 'last_updated' => time());
   *    $i++;
   *  }
   */
  $seeds = array();

  # Constraints
  # Put here the constraints
  # Example:
  /*
    $constaints = array(
        'primary_key' => 'id',
        'unique'      => array('date_created','last_updated'),  //stupid example but serves as proof of concept
        'foreign_key' => array(
            'key'=> $this_table_key,
            'table'=>$other_table_name,
            'reference'=> $other_table_key,
            'options'  => 'Example: on update cascade text'
            )
    );
  */
  $constaints = array();
  return array(
        'fields' => $fields,
        'seeds'  => $seeds
  );

?>