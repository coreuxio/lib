<?php
namespace Coreux\Lib\API\Contracts;

interface PaginationTransformer{
    public function transform(array $data):array;
}
