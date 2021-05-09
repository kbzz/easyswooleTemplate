<?php


namespace App\Model\Mysql;

use EasySwoole\ORM\AbstractModel;

class BaseModel extends AbstractModel
{
    protected $pk = 'id';
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';


    // 获取单字段数据
    public static function getAlone($where, $field = 'id')
    {
        return self::create()->where($where)->val($field);
    }

    // 获取单组数据
    public static function getOne($where, $field = '*', $order = ['id' => 'DESC'])
    {
        $model = self::create()->field($field);

        if (!empty($order)) {
            foreach ($order as $index => $item) {
                $model = $model->order($index, $item);
            }
        }
        return $model->where($where)->get();
    }

    // 获取单列数据
    public static function getColumn($where, $field = 'id', $order = ['id' => 'DESC'])
    {

        $model = self::create()->field($field);
        if (!empty($order)) {
            foreach ($order as $index => $item) {
                $model = $model->order($index, $item);
            }
        }
        $res = $model->all($where);
        if (empty($res)) return [];

        return array_column($res, $field);
    }

    public static function getValue($where, $field = 'id', $order = ['id' => 'DESC'])
    {
        $model = self::create()->field($field);
        if (!empty($order)) {
            foreach ($order as $index => $item) {
                $model = $model->order($index, $item);
            }
        }
        return $model->where($where)->val($field);
    }


    // 获取多条数据
    public static function getList($where = [], $field = '*', $limit = [], $order = ['id' => 'DESC'])
    {
        $model = self::create()->field($field);

        if (!empty($order)) {
            foreach ($order as $index => $item) {
                $model = $model->order($index, $item);
            }
        }
        if (!empty($limit[0])) {

            $model = $model->limit($limit[0]);
        }
        if (isset($limit[0]) && !empty($limit[1])) {
            $model = $model->limit($limit[0], $limit[1]);
        }
        return $model->all($where);
    }

    // 获取分页数据
    public static function getPaginate($where = [], $field = '*', $page = 1, $limit = 10, $order = ['id' => 'DESC'])
    {
        $model = self::create()->field($field)->limit($limit * ($page - 1), $limit)->withTotalCount();
        if (!empty($where)) {
            $model = $model->where($where);
        }
        if (!empty($order)) {
            foreach ($order as $index => $item) {
                $model = $model->order($index, $item);
            }
        }
        $data = $model->all();
        $result = $model->lastQueryResult();

        return ['page' => $page, 'total' => $result->getTotalCount(), 'data' => $data];
    }

    // 添加数据
    public static function addData($data)
    {
        return self::create()->create($data)->save();
    }

    public static function addAll($data, $replace = false)
    {
        return self::create()->saveAll($data, $replace, $transaction = true);
    }

    // 编辑数据
    public static function editData($data, $where)
    {
        return self::create()->update($data, $where);
    }

    // 删除数据
    public static function deleteData($where)
    {

        return self::create()->destroy($where);
    }

    // 查询该数据是否存在
    public static function checkRowExists($where, $filed = 'id')
    {
        return self::create()->where($where)->val($filed);
    }

    // 求总数
    public static function getCount($where, $field = 'id')
    {
        return self::create()->where($where)->count($field);
    }

    // 求和
    public static function getSum($where, $field = 'id')
    {
        return self::create()->where($where)->sum($field);
    }

    // 求最大值
    public static function getMax($where, $field = 'id')
    {
        return self::create()->where($where)->max($field);
    }

    // 求最小值
    public static function getMin($where, $field = 'id')
    {
        return self::create()->where($where)->min($field);
    }

    // 求平均值
    public static function getAvg($where, $field = 'id')
    {
        return self::create()->where($where)->avg($field);
    }

    // 自增数据
    public static function incValue($where, $field = "id", $num = 1)
    {
        $value = self::getValue($where, $field);
        return self::editData([$field => $value + $num], $where);
    }

    // 自减数据
    public static function decValue($where, $field = "id", $num = 1)
    {
        $value = self::getValue($where, $field);
        return self::editData([$field => $value - $num], $where);
    }

    // 添加数据,如果存在就修改
    public static function addAndUpdate($data, $where = [])
    {
        if (empty($where)) {
            return self::addData($data);
        }
        $res = self::getOne($where);
        if (empty($res)) {
            return self::addData($data);
        } else {
            self::editData($data, $where);
            return $res['id'];
        }
    }
}