<?php
namespace Nora\Data\DataSource\Adapter;

use Nora\Base\Component;
use Nora\Base\Hash;
use Nora\Util\Spec\SpecLine;
use Nora;

/**
 * データソースアダプター
 */
interface AdapterIF
{
    public function count($query = []);

    public function findOne($query = []);

    public function insert($datas);

    public function delete($query);

    public function update($query, $datas);

    public function find($query, $options = []);
}
