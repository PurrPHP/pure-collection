Collections has basic methods described in AbstractCollection

In PHP there are no way to have strict property only except describe method argument types.

List contains only array values without string keys - it's handled in AbstractList by array_values(), other collection don't call this method, they pass given value as is.

Set contains only uniq values - to filter them we must have special function to filter uniq values, cause in the common way we manage objects collection with special criteria (for user it can be uuid, id or email (use email here email - wrong pattern :)). Так появилась функция AbstractCollection::filterUniqValuesю

For удобства mutable collections implement \ArrayAcces, to allow set values like `$map['a'] = 1;`. 

ArrayAcces has describe methods and it's not possible override them.
`public function offsetSet(mixed $offset, mixed $value): void`


For manage types strictness we've added 

`abstract protected function ensureType(mixed $value): void;`

Mutable lists have only int indexes, so we must check passed key in offsetSet - added `ensureIntOffset`. In mutable list we can set value only for existed index - added `ensureIndexInBounds`.


How to implement your own collection.

Implemnent childern for target collection type: AbstractList, AbstractSet, AbstractMap, etc. 

Implement:
- constructor with strict type, 
- `filterUniqValues`.  

For mutable list, map implement:
- `ensureType`

Use only exceptions from `src/Exception`.

There is no AbstractMutableSet cause set has only one difference with list - stores unique values and domain specification is more difficult than set specification. In another words it's simplier to implement your own mutableSet. See IntMutableSet as reference. This set contains a lot of int functions and few guards to store unique values after mutations. 
