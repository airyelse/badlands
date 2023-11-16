<?php

namespace Dcat\Admin\Badlands\Forms\Fields;


use Illuminate\Support\Collection;

trait HasFieldProperties
{
    protected ?Collection $properties = null;

    public function getProperties(): Collection
    {
        if ($this->properties == null) {
            $this->properties = Collection::make();
        }
        return $this->properties;
    }

    public function setProperties(Collection $properties): static
    {
        $this->properties = $properties;
        return $this;
    }

    public function set($key, $value): static
    {
        if ($this->properties == null) {
            $this->properties = Collection::make();
        }
        $this->properties->put($key, $value);
        return $this;
    }

    public function get($key, $default = null)
    {
        if ($this->properties == null || !$this->properties->has($key)) {
            return $default;
        }
        return $this->properties->get($key, $default);
    }

}
