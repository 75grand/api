<?php

namespace App\Support;

class PrerequisiteLogic
{
    /**
     * @see https://www.notion.so/jeromepaulos/63dc008b0986498486dc58b263b69b41
     * @param array[] $logic
     * @param array[] $prerequisites
     */
    public static function evaluate(array $logic, array $prerequisites): bool
    {
        $result = true;

        foreach($logic['values'] as $value) {
            $ruleResult = is_array($value)
                ? self::evaluate($logic, $prerequisites)
                : array_filter($prerequisites, function($prerequisite) use ($value) {
                    return $prerequisite['subject'] === $value['subject'] &&
                        $prerequisite['course_number'] === $value['course_number'] &&
                            $prerequisite['test'] === $value['test'];
                }) !== [];

            if($logic['rule'] === 'AND') {
                $result = $result && $ruleResult;
            } else {
                if($ruleResult) return true;
                $result = false;
            }
        }

        return $result;
    }
}