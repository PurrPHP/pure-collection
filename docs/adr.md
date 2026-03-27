Collections has basic methods described in AbstractCollection

In PHP there are no way to have strict property only except describe method argument types.

List contains only array values without string keys - it's handled in AbstractList by array_values(), other collection don't call this method, they pass given value as is.

Set contains only uniq values - to filter them we must have special function to filter uniq values, cause in the common way we manage objects collection with special criteria (for user it can be uuid, id or email (use email here email - wrong pattern :)). Так появилась функция AbstractCollection::filterUniqValuesю

For удобства mutable collections implement \ArrayAcces, to allow set values like `$map['a'] = 1;`. 

ArrayAcces has describe methods and it's not possible override them.
`public function offsetSet(mixed $offset, mixed $value): void`


For manage types strictness we've added 

`abstract protected function ensureType(mixed $value): void;`

To push use specific 


        if (null !== $offset) {
            $this->ensureIntOffset($offset);
            $this->ensureIndexInBounds($offset);
        }

        $this->ensureType($value);
