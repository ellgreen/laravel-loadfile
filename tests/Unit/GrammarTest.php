<?php

namespace Tests\Unit;

use EllGreen\LaravelLoadFile\Builder\Builder;
use EllGreen\LaravelLoadFile\Builder\FileType;
use EllGreen\LaravelLoadFile\Exceptions\CompilationException;
use EllGreen\LaravelLoadFile\Grammar;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Query\Expression;
use PHPUnit\Framework\TestCase;

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

    public function testNoFileThrowsException()
    {
        $this->expectException(CompilationException::class);

        $builder = (new Builder(
            $this->createMock(DatabaseManager::class),
            $this->createMock(Grammar::class),
        ))->into('table');

        $this->grammar->compileLoadFile($builder);
    }

    public function testNoTableThrowsException()
    {
        $this->expectException(CompilationException::class);

        $builder = (new Builder(
            $this->createMock(DatabaseManager::class),
            $this->createMock(Grammar::class),
        ))->file('path/to/file');

        $this->grammar->compileLoadFile($builder);
    }

    public function testNoTableOrFileThrowsException()
    {
        $this->expectException(CompilationException::class);

        $builder = new Builder(
            $this->createMock(DatabaseManager::class),
            $this->createMock(Grammar::class),
        );

        $this->grammar->compileLoadFile($builder);
    }

    public function testSimpleCompile()
    {
        $this->assertSqlAndBindings(<<<'SQL'
            load data local infile '/path/to/employees.csv'
            into table `employees` (`forename`, `surname`, `employee_id`)
        SQL);
    }

    public function testSimpleXmlCompile()
    {
        $this->builder->fileType(FileType::XML);

        $this->assertSqlAndBindings(<<<'SQL'
            load xml local infile '/path/to/employees.csv'
            into table `employees` (`forename`, `surname`, `employee_id`)
        SQL);
    }

    public function testCompileWithRowsIdentifiedBy()
    {
        $this->builder->fileType(FileType::XML)
            ->rowsIdentifiedBy('<tag>');

        $this->assertSqlAndBindings(<<<'SQL'
            load xml local infile '/path/to/employees.csv' into table `employees`
            rows identified by '<tag>'
            (`forename`, `surname`, `employee_id`)
        SQL);
    }

    public function testCompileWithNoColumns()
    {
        $this->builder->columns(null);

        $this->assertSqlAndBindings(<<<'SQL'
            load data local infile '/path/to/employees.csv' into table `employees`
        SQL);
    }

    public function testReplace()
    {
        $this->builder->replace();

        $this->assertSqlAndBindings(<<<'SQL'
            load data local infile '/path/to/employees.csv' replace
            into table `employees` (`forename`, `surname`, `employee_id`)
        SQL);
    }

    public function testIgnore()
    {
        $this->builder->ignore();

        $this->assertSqlAndBindings(<<<'SQL'
            load data local infile '/path/to/employees.csv' ignore
            into table `employees` (`forename`, `surname`, `employee_id`)
        SQL);
    }

    public function testLowPriority()
    {
        $this->builder->lowPriority();

        $this->assertSqlAndBindings(<<<'SQL'
            load data low_priority local infile '/path/to/employees.csv'
            into table `employees` (`forename`, `surname`, `employee_id`)
        SQL);
    }

    public function testConcurrent()
    {
        $this->builder->concurrent();

        $this->assertSqlAndBindings(<<<'SQL'
            load data concurrent local infile '/path/to/employees.csv'
            into table `employees` (`forename`, `surname`, `employee_id`)
        SQL);
    }

    public function testIgnoreLines()
    {
        $this->builder->ignoreLines(1);

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

    public function testCompileWithCharset()
    {
        $this->builder->charset('utf8mb4');

        $this->assertSqlAndBindings(<<<'SQL'
            load data local infile '/path/to/employees.csv'
            into table `employees` character set 'utf8mb4'
            (`forename`, `surname`, `employee_id`)
        SQL);
    }

    public function testCompileWithAllOptions()
    {
        $this->builder
            ->replace()
            ->charset('utf8mb4')
            ->fields(',', '"', '\\\\', true)
            ->lines('', '\\n')
            ->ignoreLines(1)
            ->set([
                'name' => new Expression("concat(@forename, ' ', @surname)"),
                'department' => 'sales',
            ]);

        $this->assertSqlAndBindings(<<<'SQL'
            load data local infile '/path/to/employees.csv'
            replace into table `employees` character set 'utf8mb4'
            fields terminated by ',' optionally enclosed by '"' escaped by '\\'
            lines starting by '' terminated by '\n'
            ignore 1 lines
            (`forename`, `surname`, `employee_id`)
            set `name` = concat(@forename, ' ', @surname), `department` = ?
        SQL, ['sales']);
    }

    private function assertSqlAndBindings(string $expectedSql, ?array $expectedBindings = null): void
    {
        $expectedSql = trim(
            str_replace('    ', '', str_replace("\n", ' ', $expectedSql))
        );

        $query = $this->grammar->compileLoadFile($this->builder);

        $this->assertSame($expectedSql, $query->getSql());

        if (isset($expectedBindings)) {
            $this->assertSame($expectedBindings, $query->getBindings());
            return;
        }

        $this->assertEmpty($query->getBindings());
    }
}
