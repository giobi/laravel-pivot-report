<?php


namespace Giobi\Pivot;


class Pivot {

    private $rowModel;
    private $values = [];
    private $summaryOperator = 'sum';
    private $summaryOperators = ['sum', 'count', 'avg'];

    private $valueFilters = [];
    private $rowFilters = [];

    private $rowQuery = [];

    public function __construct(string $rowModel = null, string $summaryOperator = 'sum') {

        $this->setRowModel($rowModel);
        $this->setSummaryOperator($summaryOperator);

        $this->resetQueries();
    }

    public function setRowModel(string $rowModel): self {
        $this->rowModel = $rowModel;
        return $this;
    }

    public function setSummaryOperator(string $summaryOperator): self {
        $this->summaryOperator = $summaryOperator;
        return $this;
    }

    public function resetQueries(): self {

        $this->valueQuery = ($this->valueModel)::query();

        if (null !== $this->rowModel)
            $this->rowQuery = ($this->rowModel)::query();

        return $this;
    }

    public function addValue(string $label, string $relation, string $field, string $summary = 'sum'): self {
        $this->values[$label] = [
            'label' => $label,
            'relation' => $relation,
            'field' => $field,
            'summary' => $summary,
        ];
        return $this;
    }

    public function filterRowsWhere($field, $compareValue, $value = null): self {
        $this->rowQuery->where($field, $compareValue, $value);
        return $this;
    }

    public function filterValuesWhere($field, $compareValue, $value = null) {
        $this->valueQuery->where($field, $compareValue, $value);
        return $this;
    }

    public function getCollection() {
        $returnCollection = collect();

        foreach ($this->getRowModels() as $rowModel) {
            $rowData = [];

            $rowData['model'] = $rowModel;
            $rowData['values'] = $this->getValuesForRow($rowModel);

            $returnCollection->push($rowData);
        }

        return $returnCollection;
    }

    private function getRowModels() {
        return $this->rowQuery->get();
    }


}
