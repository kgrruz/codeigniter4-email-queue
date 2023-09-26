# Codeigniter4 email queue
Queue, preview and and send emails stored in the database.

This package provides an interface for creating emails on the fly and
store them in a queue to be processed later by an offline worker using a
Codeigniter4 CLI command.

## Requirements ##
- Codeigniter 4.x
- codeigniter4/settings

## Installation ##

```sh
composer require kgrruz/codeigniter4-email-queue
```

### run migrations command:

```sh
php spark migrate -n EmailQueue
```

## Usage

You must set these configuration in the **app/Config/Email.php** or set these values using the [codeigniter4/settings](https://github.com/codeigniter4/settings) package before using it:

```sh
    setting('Email.fromEmail')
    setting('Email.fromName')
    setting('Email.protocol')
```  

In case of SMTP protocol:

```sh
    setting('Email.SMTPHost')
    setting('Email.SMTPPort')
    setting('Email.SMTPUser')
    setting('Email.SMTPPass')
    setting('Email.SMTPCrypto')
    setting('Email.SMTPTimeout')
    setting('Email.SMTPKeepAlive')
```    

Default email settings:

```sh
    $config['mailpath'] = '/usr/sbin/sendmail';
    $config['wordWrap'] = true;
    $config['mailType'] = 'html';
```  

Whenever you need to send an email, use the EmailQueue model to create
and queue a new one by storing the correct data:

    use EmailQueue\EmailQueue;
    EmailQueue::enqueue($to, $subject, $attachments, $data);

`enqueue` method receives 3 arguments:

- First argument is a string of email addresses that will be treated as recipients.
- Second argument is an string with the email subject
- Third argument is an array of files paths
- Fourth argument is an array of view variables to be passed to the
  email template
 * `message`: Email's body text/html
 
    $data['message'] = "hello world message";


### Sending emails

Emails should be sent using bundled Sender command, use `-l` option to
limit the number of emails processed

	# php spark emails:send -l 10

You can configure this command to be run under a cron or any other tool
you wish to use.

# Todo

- Priority
- BCC,CC

# Contributing

## Run the tests

```
./vendor/bin/phpunit tests/
```
