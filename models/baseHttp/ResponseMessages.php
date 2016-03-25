<?php

namespace app\models\baseHttp;

class ResponseMessages {

  public function getMessages() {
    return $this->messages;
  }

  /**
   * model, function, id, result
   */
  public function __construct() {
    $messages = array();

    $messages['allModels']['allOperations']['unknown.storage.exception']['1'] = 'message-Error_Unknown_Storage_Exception';

    $messages['Feature']['create']['entity.exists']['1'] = 'message-Notify_Create_Feature_Code_Duplicate';

    $messages['NetworkGroupManagement']['delete']['notify_Successful']['0'] = 'message-Notify_Network_Group_Deleted';
    $messages['NetworkGroupManagement']['delete']['fk.constraint.violation']['1'] = 'message-Error_Delete_Network_Group_Being_Used';

    $this->messages = $messages;
  }
}
