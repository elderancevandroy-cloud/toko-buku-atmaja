<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class FormBuilder
{
    protected $fields = [];
    protected $model;
    protected $action;
    protected $method = 'POST';
    protected $enctype = 'application/x-www-form-urlencoded';
    protected $submitLabel = 'Submit';
    protected $cancelUrl;

    /**
     * Set the model for the form
     */
    public function setModel($model = null)
    {
        $this->model = $model;
        return $this;
    }

    /**
     * Set form action and method
     */
    public function setAction($action, $method = 'POST')
    {
        $this->action = $action;
        $this->method = strtoupper($method);
        return $this;
    }

    /**
     * Set form encoding type
     */
    public function setEnctype($enctype)
    {
        $this->enctype = $enctype;
        return $this;
    }

    /**
     * Set submit button label
     */
    public function setSubmitLabel($label)
    {
        $this->submitLabel = $label;
        return $this;
    }

    /**
     * Set cancel URL
     */
    public function setCancelUrl($url)
    {
        $this->cancelUrl = $url;
        return $this;
    }

    /**
     * Add a generic field to the form
     */
    public function addField($type, $name, $label, $options = [])
    {
        $field = [
            'type' => $type,
            'name' => $name,
            'label' => $label,
            'value' => $this->getFieldValue($name, $options['value'] ?? null),
            'required' => $options['required'] ?? false,
            'placeholder' => $options['placeholder'] ?? null,
            'class' => $options['class'] ?? null,
            'attributes' => $options['attributes'] ?? [],
        ];

        // Add type-specific options
        if ($type === 'select') {
            $field['options'] = $options['options'] ?? [];
            $field['empty_option'] = $options['empty_option'] ?? null;
        }

        if ($type === 'textarea') {
            $field['rows'] = $options['rows'] ?? 3;
            $field['cols'] = $options['cols'] ?? null;
        }

        if (in_array($type, ['number', 'range'])) {
            $field['min'] = $options['min'] ?? null;
            $field['max'] = $options['max'] ?? null;
            $field['step'] = $options['step'] ?? null;
        }

        $this->fields[] = $field;
        return $this;
    }

    /**
     * Add an input field
     */
    public function addInput($name, $label, $type = 'text', $options = [])
    {
        return $this->addField($type, $name, $label, $options);
    }

    /**
     * Add a select field
     */
    public function addSelect($name, $label, $selectOptions = [], $options = [])
    {
        $options['options'] = $selectOptions;
        return $this->addField('select', $name, $label, $options);
    }

    /**
     * Add a textarea field
     */
    public function addTextarea($name, $label, $options = [])
    {
        return $this->addField('textarea', $name, $label, $options);
    }

    /**
     * Add a hidden field
     */
    public function addHidden($name, $value = null)
    {
        return $this->addField('hidden', $name, '', ['value' => $value]);
    }

    /**
     * Add a checkbox field
     */
    public function addCheckbox($name, $label, $value = 1, $options = [])
    {
        $options['value'] = $value;
        return $this->addField('checkbox', $name, $label, $options);
    }

    /**
     * Add a radio field
     */
    public function addRadio($name, $label, $radioOptions = [], $options = [])
    {
        $options['options'] = $radioOptions;
        return $this->addField('radio', $name, $label, $options);
    }

    /**
     * Get field value from model or old input
     */
    protected function getFieldValue($name, $default = null)
    {
        // First check old input (for validation errors)
        if (old($name) !== null) {
            return old($name);
        }

        // Then check model value
        if ($this->model && $this->model instanceof Model) {
            return $this->model->getAttribute($name) ?? $default;
        }

        return $default;
    }

    /**
     * Get all fields
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Get form configuration
     */
    public function getFormConfig()
    {
        return [
            'action' => $this->action,
            'method' => $this->method,
            'enctype' => $this->enctype,
            'submit_label' => $this->submitLabel,
            'cancel_url' => $this->cancelUrl,
        ];
    }

    /**
     * Check if form uses file upload
     */
    public function hasFileUpload()
    {
        foreach ($this->fields as $field) {
            if ($field['type'] === 'file') {
                return true;
            }
        }
        return false;
    }

    /**
     * Get HTTP method for form
     */
    public function getHttpMethod()
    {
        return in_array($this->method, ['GET', 'POST']) ? $this->method : 'POST';
    }

    /**
     * Check if method spoofing is needed
     */
    public function needsMethodSpoofing()
    {
        return !in_array($this->method, ['GET', 'POST']);
    }

    /**
     * Render the form
     */
    public function render($view = 'components.form', $data = [])
    {
        return view($view, array_merge([
            'fields' => $this->fields,
            'form_config' => $this->getFormConfig(),
            'model' => $this->model,
            'has_file_upload' => $this->hasFileUpload(),
            'http_method' => $this->getHttpMethod(),
            'needs_method_spoofing' => $this->needsMethodSpoofing(),
        ], $data));
    }

    /**
     * Generate form HTML directly
     */
    public function toHtml($view = 'components.form')
    {
        return $this->render($view)->render();
    }

    /**
     * Clear all fields
     */
    public function clearFields()
    {
        $this->fields = [];
        return $this;
    }

    /**
     * Remove a field by name
     */
    public function removeField($name)
    {
        $this->fields = array_filter($this->fields, function ($field) use ($name) {
            return $field['name'] !== $name;
        });
        return $this;
    }

    /**
     * Get field by name
     */
    public function getField($name)
    {
        foreach ($this->fields as $field) {
            if ($field['name'] === $name) {
                return $field;
            }
        }
        return null;
    }

    /**
     * Update field options
     */
    public function updateField($name, $options = [])
    {
        foreach ($this->fields as &$field) {
            if ($field['name'] === $name) {
                $field = array_merge($field, $options);
                break;
            }
        }
        return $this;
    }
}