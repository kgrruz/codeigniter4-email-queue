<?php

namespace EmailQueue\Models;

use CodeIgniter\Model;
use CodeIgniter\I18n\Time;

/**
 * EmailQueue Model.
 */
class EmailQueueModel extends Model
{

    protected $table      = 'email_queue';
    protected $primaryKey = 'id';

    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'email',
        'from_name',
        'from_email',
        'subject',
        'message',
        'sent',
        'send_at',
        'attempts',
        'attachments',
        'created_at'
    ];

    protected $useTimestamps = false;

    /**
     * Stores a new email message in the queue.
     *
     * @param mixed $to      email recipient
     * @param string $subject   email subject
     * @param array $data    associative array of variables to be passed to the email template
     * @param array $options list of options for email sending. Possible keys:
     *
     */
    public function enqueue($to, string $subject,array $data): bool
    {

        helper('setting');

        $defaults = [
          'email' => $to,
          'subject' => $options['subject'],
          'from_name'=>setting('Email.fromName'),
          'from_email'=>setting('Email.fromEmail'),
          'message'=>$data['message'],
          'created_at' => new Time('now'),
          'attempts' => 0,
          'sent' => 0
        ];

        return $this->insert($defaults);

    }

    /**
     * Returns a list of queued emails that needs to be sent.
     *
     * @param int|string $size number of unset emails to return
     * @return array list of unsent emails
     */
    public function getBatch($size = 10): array
    {

      return $this->asArray()
                 ->where('sent', 0)
                 ->where('attempts <=', 3)
                 ->limit($size)
                 ->orderBy('created_at','ASC')
                 ->findAll();

    }

}
