<?php
namespace Coreux\lib\API\Contracts;

interface PaginationTransformer{
    public function transform(array $data):array;
}
