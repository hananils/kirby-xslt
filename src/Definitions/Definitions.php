<?php

namespace Hananils\Definitions;

use Exception;
use Kirby\Data\Yaml;
use Kirby\Filesystem\F;

class Definitions
{
    protected $name;
    protected $definitions = [];

    public function __construct($name = 'default')
    {
        $this->name = strtolower($name);
        $this->definitions = $this->load($this->file());
    }

    public function load($file, $extend = true, $normalize = true)
    {
        if ($file === null) {
            return [];
        }

        $yaml = F::read($file);
        $definitions = Yaml::decode($yaml);

        if ($extend === true) {
            $definitions = $this->extend($definitions);
        }

        if ($normalize === true) {
            $definitions = $this->normalize($definitions);
        }

        return $definitions;
    }

    public function extend($definitions)
    {
        if (isset($definitions['extends'])) {
            $definitions = $this->extendNode('', $definitions['extends'], $definitions);
        }

        foreach ($definitions as $node => $definition) {
            if (isset($definition['extends'])) {
                $definitions[$node] = $this->extendNode('pages', $definition['extends'], $definition);
            }

            if (isset($definition['files']) && is_array($definition['files'])) {
                foreach ($definition['files'] as $template => $files) {
                    if (isset($files['extends'])) {
                        $definitions[$node]['files'][$template] = $this->extendNode('files', $files['extends'], $files);
                    }
                }
            }
        }

        return $definitions;
    }

    public function extendNode($type, $path, $definition)
    {
        $file = F::realpath($this->root() . '/' . $path . '.yml', $this->root() . '/' . $type);
        $extends = $this->load($file);

        unset($definition['extends']);

        return array_replace_recursive($extends, $definition);
    }

    public function normalize($definitions)
    {
        foreach ($definitions as $node => $definition) {
            if (isset($definition['content']) && is_string($definition['content'])) {
                $definitions[$node]['content'] = $this->normalizeFields($definition['content']);
            }

            if (isset($definition['children']['content']) && is_string($definition['children']['content'])) {
                $definitions[$node]['children']['content'] = $this->normalizeFields($definition['children']['content']);
            }

            if (isset($definition['modules']['content']) && is_string($definition['modules']['content'])) {
                $definitions[$node]['modules']['content'] = $this->normalizeFields($definition['modules']['content']);
            }

            if (isset($definition['files']) && is_array($definition['files'])) {
                foreach ($definition['files'] as $template => $files) {
                    if (isset($files['meta']) && is_string($files['meta'])) {
                        $definitions[$node]['files'][$template]['meta'] = $this->normalizeFields($files['meta']);
                    }
                }
            }
        }

        return $definitions;
    }

    public function normalizeFields($fields)
    {
        $fields = explode(',', $fields);
        $fields = array_map('trim', $fields);
        $fields = array_fill_keys($fields, true);

        return $fields;
    }

    public function file()
    {
        try {
            return F::realpath($this->root() . '/' . $this->name() . '.yml', $this->root());
        } catch (Exception $e) {
            try {
                return F::realpath($this->root() . '/default.yml', $this->root());
            } catch (Exception $e) {
                return null;
            }
        }
    }

    public function root()
    {
        return kirby()->root('site') . '/definitions';
    }

    public function name()
    {
        return $this->name;
    }

    public function get($node = false)
    {
        if (!$node) {
            return $this->definitions;
        } elseif (array_key_exists($node, $this->definitions)) {
            return $this->definitions[$node];
        } else {
            false;
        }
    }
};
