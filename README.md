# Codeigniter4 email queue
Queue, preview and and send emails stored in the database.

This package provides an interface for creating emails on the fly and
store them in a queue to be processed later by an offline worker using a
Codeigniter4 CLI command.

## Installation ##


```sh
composer require kgrruz/codeigniter4-email-queue
```


php spark migrate -n EmailQueue


## Usage

Whenever you need to send an email, use the EmailQueue model to create
and queue a new one by storing the correct data:

    use EmailQueue\EmailQueue;
    EmailQueue::enqueue($to, $data, $options);

`enqueue` method receives 3 arguments:

- First argument is a string or array of email addresses that will be treated as recipients.
- Second arguments is an array of view variables to be passed to the
  email template
- Third arguments is an array of options, possible options are
 * `subject`: Email's subject
 * `send_at`: date time sting representing the time this email should be sent at (in UTC)
 * `format`: Type of template to use (html, text or both)
 * `from_name`: String with from name. Must be supplied together with `from_email`.
 * `from_email`: String with from email. Must be supplied together with `from_name`.


### Sending emails

Emails should be sent using bundled Sender command, use `-l` option to
limit the number of emails processed

	# php spark emails:send -l 10

You can configure this command to be run under a cron or any other tool
you wish to use.

# Contributing

## Run the tests

```
./vendor/bin/phpunit tests/
```
