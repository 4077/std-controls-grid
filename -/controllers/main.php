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
        $columns = handlers()->render($this->data('handlers/fields'));

        $output = [];

        foreach ($columns as $columnId => $column) {
            $output[$columnId] = $this->fixControl($column);
        }

        return $output;
    }

    private function fixControl($column)
    {
        if (!empty($column['control'])) {
            $control = &$column['control'];

            $controlsData = $this->getControlsData();

            if ($controlCall = ap($controlsData, $control[0])) {
                $control[0] = $controlCall['path'];

                $controlData = $control[1] ?? [];

                ra($controlData, $controlCall['data'] ?? []);

                $control[1] = [
                    'model' => '%model',
                    'field' => '%column_id',
                    'data'  => $controlData
                ];
            }
        }

        return $column;
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
