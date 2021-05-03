<?php

namespace Tests\Unit;

use EllGreen\LaravelLoadFile\Builder;
use EllGreen\LaravelLoadFile\Grammar;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Query\Expression;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertEmpty;

class GrammarTest extends TestCase
{
    private Grammar $grammar;
    private Builder $builder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->grammar = new Grammar();
        $this->builder = new Builder(
            $this->createMock(DatabaseManager::class),
            $this->createMock(Grammar::class),
        );

        $this->builder->file('/path/to/employees.csv', $local = true)
            ->into('employees')
            ->columns(['forename', 'surname', 'employee_id']);
    }

    public function testSimpleCompile()
    {
        $this->assertSqlAndBindings(<<<'SQL'
            load data local infile '/path/to/employees.csv'
            into table `employees` (`forename`, `surname`, `employee_id`)
        SQL);
    }

    public function testIgnoreLines()
    {
        $this->builder->ignore(1);

        $this->assertSqlAndBindings(<<<'SQL'
            load data local infile '/path/to/employees.csv'
            into table `employees` ignore 1 lines
            (`forename`, `surname`, `employee_id`)
        SQL);
    }

    public function testFieldsTerminatedBy()
    {
        $this->builder->fieldsTerminatedBy(',');

        $this->assertSqlAndBindings(<<<'SQL'
            load data local infile '/path/to/employees.csv'
            into table `employees` fields terminated by ','
            (`forename`, `surname`, `employee_id`)
        SQL);
    }

    public function testFields()
    {
        $this->builder->fields(',', '"', '\\\\', true);

        $this->assertSqlAndBindings(<<<'SQL'
            load data local infile '/path/to/employees.csv'
            into table `employees` fields terminated by ','
            optionally enclosed by '"' escaped by '\\'
            (`forename`, `surname`, `employee_id`)
        SQL);
    }

    public function testLines()
    {
        $this->builder->lines('', '\\n');

        $this->assertSqlAndBindings(<<<'SQL'
            load data local infile '/path/to/employees.csv'
            into table `employees`
            lines starting by '' terminated by '\n'
            (`forename`, `surname`, `employee_id`)
        SQL);
    }

    public function testCompileWithSet()
    {
        $this->builder->set([
            'name' => new Expression("concat(@forename, ' ', @surname)"),
            'department' => 'sales',
        ]);

        $this->assertSqlAndBindings(<<<'SQL'
            load data local infile '/path/to/employees.csv'
            into table `employees` (`forename`, `surname`, `employee_id`)
            set `name` = concat(@forename, ' ', @surname), `department` = ?
        SQL, ['sales']);
    }

    private function assertSqlAndBindings(string $expectedSql, ?array $expectedBindings = null): void
    {
        $expectedSql = trim(
            str_replace('    ', '', str_replace("\n", ' ', $expectedSql))
        );

        list($sql, $bindings) = $this->grammar->compileLoadFile($this->builder);

        $this->assertSame($expectedSql, $sql);

        if (isset($expectedBindings)) {
            $this->assertSame($expectedBindings, $bindings);
            return;
        }

        $this->assertEmpty($bindings);
    }
}
