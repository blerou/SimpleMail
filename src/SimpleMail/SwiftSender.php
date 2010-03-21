<?php

/*
 * This file is part of the SimpleMail library.
 *
 * Copyright (c) 2009-2010 Szabolcs Sulik <sulik.szabolcs@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

class SimpleMail_SwiftSender extends SimpleMail_SenderImp
{
    /**
     * @var Swift_Transport
     */
    protected $transport = null;

    /**
     * @var Swift_Mailer
     */
    protected $mailer = null;

    /**
     * @var Swift_Message
     */
    protected $message = null;

    /**
     * data of embedded images
     */
    protected $embedded = array();
    protected $embeddedUrl = null;
    protected $embeddedDir = null;

    /**
     * send
     *
     * @return void
     */
    public function send()
    {
        try {
            $this->message = Swift_Message::newInstance()
                ->setSubject($this->template->getSubject())
                ->setCharset("utf-8");
            switch ($this->type) {
                case self::TYPE_PLAIN :
                    $this->message->setBody($this->template->getPlain(), 'text/plain');
                    break;

                case self::TYPE_HTML :
                    $html_body = $this->template->getHtml();
                    if ($this->hasEmbedImage()) {
                        $html_body = $this->embedImages($html_body);
                    }
                    $this->message->setBody($html_body, 'text/html');
                    break;

                case self::TYPE_BOTH :
                default :
                    $html_body = $this->template->getHtml();
                    if ($this->hasEmbedImage()) {
                        $html_body = $this->embedImages($html_body);
                    }
                    $this->message->setBody($html_body, 'text/html');
                    $this->message->addPart($this->template->getPlain(), 'text/plain');
                    break;
            }

            if ($this->getAttribute('reply')) {
                $this->message->setReplyTo($this->getAttribute('reply'));
            }

            $this->message
                ->setFrom($this->getAttribute('from'))
                ->setTo($this->getAttribute('to'));
            $this->getMailer()->send($this->message);
        } catch (Exception $exception) {
            if ($this->getTransport() && $this->getTransport()->isStarted()) {
                $this->disconnect();
            }

            throw $exception;
        }
    }

    public function getMailer()
    {
        if (!$this->mailer) {
            $this->mailer = Swift_Mailer::newInstance($this->getTransport());
        }
        return $this->mailer;
    }

    public function getTransport()
    {
        if (!$this->transport) {
            $this->setTransport(Swift_SmtpTransport::newInstance());
        }
        return $this->transport;
    }

    /**
     * Swift communication transport setter
     *
     * it also reinitialize the swift mailer instance
     *
     * @param  Swift_Transport $transport
     * @return SimpleMail_SwiftSender
     */
    public function setTransport(Swift_Transport $transport)
    {
        $this->transport = $transport;
        $this->mailer = Swift_Mailer::newInstance($transport);

        return $this;
    }

    /**
     * disconnect the Swift_Transport
     */
    public function disconnect()
    {
        $this->getTransport()->stop();
    }

    /**
     * set images to embed
     *
     * @param  array  $images
     * @param  string $url_prefix  The full url part before the name of an image
     * @param  string $temp_dir    The full path prefix of the local images
     * @return void
     */
    public function setEmbedImages(array $images, $url_prefix, $temp_dir)
    {
        $this->embedded = $images;
        $this->embeddedUrl = rtrim($url_prefix, '/') . '/';
        $this->embeddedDir = rtrim($temp_dir, '/') . '/';
    }

    /**
     * has images to embed
     *
     * @return bool
     */
    protected function hasEmbedImage()
    {
        return (bool)count($this->embedded);
    }

    /**
     * embed images to the given body
     *
     * @param  string $body
     * @return string
     */
    protected function embedImages($body)
    {
        foreach ($this->embedded as $image) {
            if (false !== strpos($body, $this->embeddedUrl . $image)) {
                $cid = $this->message->embed(Swift_Image::fromPath($this->embeddedDir . $image));
                $body = str_replace($this->embeddedUrl . $image, $cid, $body);
            }
        }

        return $body;
    }
}