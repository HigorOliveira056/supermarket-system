<?php
namespace InfraDataBase;

class Table {
    private $tableName;
    private $conn;
    private $columns = [];
    private $pks = [];
    private $fks = [];

    public function __construct (string $tableName, Connection $conn) {
        $this->tableName = $tableName;
        $this->conn = $conn;
    }

    public function setColumn (string $nameColumn, string $typeColum) : void {
        $this->columns[$nameColumn] = $typeColum;
    }

    public function setPrimaryKey (string $nameColumn, string $typeColum) : void {
        $this->setColumn($nameColumn, $typeColum);
        $this->pks[] = $nameColumn;
    }

    public function setForeignKey (string $nameColumn, string $typeColum, string $referenceTable, string $referenceColumn) : void {
        $this->setColumn($nameColumn, $typeColum);
        $this->fks[$nameColumn] = ['referenceTable' =>  $referenceTable, 'referenceColumn' => $referenceColumn];
    }

    public function up () : void {
        $conn = $this->conn->getConnection();

        $query = "SET QUOTED_IDENTIFIER ON; ";

        $query = "CREATE TABLE \"{$this->tableName}\" (";

        foreach ($this->columns as $name => $type) {
            $query .= " $name $type,";
        }
        $query = preg_replace('/,$/', '', $query);

        if (count($this->pks) > 0) {
            $query .= ", CONSTRAINT PK_primary_key_{$this->tableName} PRIMARY KEY (" ;
            foreach ($this->pks as $name) {
                $query .= "$name,";
            }
            $query = preg_replace('/,$/', '', $query) . ')';
        }

        if (count($this->fks) > 0) {
            foreach ($this->fks as $column => $references) {
                $query .= ", CONSTRAINT FK_{$column}_{$references['referenceTable']} FOREIGN KEY ($column) ".
                            "REFERENCES {$references['referenceTable']} ({$references['referenceColumn']})";
            }
        }
        
        $query .= ")";

        $conn->query($query);
    }

    public function down () : void {
        $conn = $this->conn->getConnection();
        $conn->query("DROP TABLE {$this->tableName}");
    }
}