<?php

namespace App\Traits;

trait NestedItems
{
    public function getDelimiter()
    {
        return defined('static::DELIMITER') ? static::DELIMITER : '/';
    }

    public function scopeDescendants($query, $ids)
    {
        $ids = (array)$ids;
        foreach ($ids as $k => $id) {
            $ids[$k] = intval(preg_quote($id));
        }
        return $query->whereRaw('"tree" ~* ?', [$this->getDelimiter().'(' . implode('|', $ids) . ')'.$this->getDelimiter()]);
    }

    public function scopeOrDescendants($query, $ids)
    {
        $ids = (array)$ids;
        foreach ($ids as $k => $id) {
            $ids[$k] = intval(preg_quote($id));
        }
        return $query->orWhereRaw('"tree" ~* ?', [$this->getDelimiter().'(' . implode('|', $ids) . ')'.$this->getDelimiter()]);
    }

    public function scopeChilds($query, $ids)
    {
        $ids = (array)$ids;
        foreach ($ids as $k => $id) {
            $ids[$k] = intval(preg_quote($id));
        }
        return $query->whereRaw('"tree" ~* ?', [$this->getDelimiter().'(' . implode('|', $ids) . ')'.$this->getDelimiter().'$']);
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('tree');
    }

    public function getRootAttribute()
    {
        $ar = explode($this->getDelimiter(), $this->tree);

        if (isset($ar[1]) and $ar[1] != '')
            return self::any()->find($ar[1]);
        else
            return null;
    }

    public function getParent()
    {
        $ar = explode($this->getDelimiter(), $this->tree);

        $ar = array_reverse($ar);

        if (isset($ar[1]) and $ar[1] != '')
        {
            $parent = self::find($ar[1]);

            return $parent;
        }
        else
            return null;
    }

    public function setParentAttribute($parent)
    {
        if (!is_object($parent))
            $parent = self::find($parent);

        $array = $parent->tree_array;

        if (isset($parent->id)) {
            $array[] = $parent->id;

            $str = $this->getDelimiter() . implode($this->getDelimiter(), $array) . $this->getDelimiter();

            $this->tree = $str;
            $this->level = $parent->getLevel() + 1;
        }
    }


    public function getTreeArrayAttribute()
    {
        $array = explode($this->getDelimiter(), $this->tree);

        $filtered = [];

        foreach ($array as $value) {
            if ($value != '') {
                $filtered[] = $value;
            }
        }

        return $filtered ?? [];
    }

    public function getLevel()
    {
        return count($this->treeArray);
    }

    public function getLevelWithLimitAttribute()
    {
        $max = 3;

        if ($this->level < $max)
            return $this->level;
        else
            return $max;
    }

    public function isHaveDescendant(&$descendants)
    {
        if (isset($descendants)) {
            foreach ($descendants as $descendant) {
                if (preg_match('/\\'.$this->getDelimiter() . $this->attributes['id'] . '\\'.$this->getDelimiter().'$/i', $descendant->tree)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function isRoot()
    {
        if (empty($this->tree))
            return true;
        else
            return false;
    }

    public function updateChildrenCount()
    {
        $this->children_count = self::childs($this->id)->count();
    }

    public function updateLevel()
    {
        $ar = explode($this->getDelimiter(), $this->tree);

        $count = 0;

        foreach ($ar as $v) {
            if ($v) $count++;
        }

        $this->level = $count;
        $this->save();
    }
}
