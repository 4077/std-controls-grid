<?php namespace std\controls\grid\controllers;

class Main extends \Controller
{
    public function view()
    {
        $v = $this->v('|');

        $v->assign('CONTENT', $this->c('\std\ui\grid~:view|', $this->getGridData()));

        $this->css();

        return $v;
    }

    private function getGridData()
    {
        remap($gridData, $this->data, 'defaults, set');

        $fields = $this->getFields();

        if ($this->data('reset_fields')) {
            $gridData['set']['columns'] = $fields;
        } else {
            $gridData['defaults']['columns'] = $fields;
        }

        return $gridData;
    }

    private function getFields()
    {
        $fields = handlers()->render($this->data('handlers/fields'));

        $output = [];

        foreach ($fields as $fieldId => $field) {
            $output[$fieldId] = $this->fixControl($field);
        }

        return $output;
    }

    private function fixControl($field)
    {
        if (!empty($field['control'])) {
            $fieldControl = &$field['control'];

            $controlsData = $this->getControlsData();

            if ($controlCall = ap($controlsData, $fieldControl[0])) {
                $controlPath = $controlCall['path'];
                $controlData = $controlCall['data'] ?? [];

                ra($controlData, $fieldControl[1] ?? []);

                $fieldControl[0] = $controlPath;
                $fieldControl[1] = $controlData;
            }
        }

        return $field;
    }

    private $controlsData;

    private function getControlsData()
    {
        if (null === $this->controlsData) {
            $controlsHandler = $this->data('handlers/controls') ?: 'std/controls:';

            $this->controlsData = handlers()->render($controlsHandler);
        }

        return $this->controlsData;
    }
}
