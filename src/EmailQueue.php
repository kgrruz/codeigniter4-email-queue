<?php

namespace EmailQueue;

use EmailQueue\Models\EmailQueueModel;

class EmailQueue{

  /**
       * Stores a new email message in the queue.
       *
       * @param mixed $to      email or array of emails as recipients
       * @param array $data    associative array of variables to be passed to the email template
       * @param array $options list of options for email sending. Possible keys:
       *
       * - subject : Email's subject
       * - send_at : date time sting representing the time this email should be sent at (in UTC)
       * - format: Type of template to use (html, text or both)
       *
       * @return bool
       */
  public static function enqueue($to, array $data, array $options = [])
     {
         $queue = new EmailQueueModel();
         return $queue->enqueue($to, $data, $options);
     }
}
