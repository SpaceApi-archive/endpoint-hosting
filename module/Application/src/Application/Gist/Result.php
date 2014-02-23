<?php

namespace Application\Gist;


/**
 * This class unmarshals a gist response manually. If the whole model
 * (http://developer.github.com/v3/gists/#response-2) should be modeled
 * then it would make sense to annotate the important/interesting
 * attributes.
 *
 * @package  Application\Gist
 *
 * @property int id
 * @property int status
 * @property string gistFile
 * @property array content
 * @property array url
 */
class Result
{
    protected $id;
    protected $status;
    protected $gistFile;
    protected $content;
    protected $url = array(
        'html' => '',
        'raw'  => ''
    );

    public function __get($property)
    {
        if (property_exists($this, $property))
            return $this->$property;

        return false;
    }

    public function __construct($status, $content)
    {
        $this->status = (int) $status;
        $this->content = json_decode($content, TRUE);
        $this->id = (int) $this->content['id'];
        $this->initGistFile();
        $this->initUrls(); // requires initGistFile() executed before
    }

    /**
     * Initializes the gist filename.
     */
    protected function initGistFile()
    {
        $files = @$this->content['files'];
        if (is_array($files)) {
            $this->gistFile = @$files['filename'];
        }
    }

    /**
     * Initializes the URLs for the html and raw resources.
     */
    protected function initUrls()
    {
        if (is_null($this->content))
            return;

        // the raw url has a trailing } which we strip later
        $gist_raw_url = @$this->content['files'][$this->gist_file]['raw_url'];
        $gist_raw_url = str_replace('}', '', $gist_raw_url);

        $this->url = array(
            'html' => @$this->content['html_url'],
            'raw' => $gist_raw_url,
        );
    }

    /**
     * Check if a private/protected member exists. This magic function
     * must be implemented in to access the properties from within the
     * template.
     *
     * @param mixed $member Property
     * @return bool
     */
    public function __isset($member)
    {
        return property_exists($this, $member);
    }
}