<?php

namespace Timedoor\TmdMembership\Macros;

use Illuminate\Http\Request;

class AuthAttemptMacro
{
    public function attempt()
    {
        return function(Request $request, Array $usernames, Array $fields) {
            $this->where(function($query) use ($request, $usernames, $fields) {
                foreach ($fields as $field) {
                    if (is_array($field)) {
                        $query->orWhere(function($andQuery) use ($request, $field, $usernames) {
                            foreach ($field as $value) {
                                $key = $usernames[$value] ?? $usernames['default'];
                                $andQuery->where($value, $request->{$key});
                            }
                        });
                    } else {
                        $key = $usernames[$field] ?? $usernames['default'];
                        $query->orWhere($field, $request->{$key});
                    }
                }
            });

            return $this;
        };
    }
}
