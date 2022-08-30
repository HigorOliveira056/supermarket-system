<?php
namespace InfraDataBase;

class Table {
    private $tableName;
    private $conn;
    private $columns = [];
    private $pks = [];
    private $fks = [];
    private $timestamps = true;

    public function __construct (string $tableName, Connection $conn, bool $timestamps = true) {
        $this->tableName = $tableName;
        $this->conn = $conn;
        $this->timestamps = $timestamps;
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

        if ($this->timestamps) {
            $this->setColumn('created_at', 'datetime');  
            $this->setColumn('updated_at', 'datetime');
        }

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

        $conn->beginTransaction();
        $conn->query($query);
        $conn->commit();
    }

    public function down () : void {
        $conn = $this->conn->getConnection();
        $conn->beginTransaction();
        $conn->query("DROP TABLE {$this->tableName}");
        $conn->commit();
    }
}