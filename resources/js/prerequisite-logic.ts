export type BooleanLogic = {
    rule: 'AND'|'OR',
    values: (BooleanLogic|Prerequisite)[]
};

export type Prerequisite = {
    subject?: number,
    course_number?: string,
    test?: string
};

/**
 * @see https://www.notion.so/jeromepaulos/63dc008b0986498486dc58b263b69b41
 */
export function evaluate(logic: BooleanLogic, prerequisites: Prerequisite[]): boolean {
    let result = true;

    for(const value of logic.values) {
        let ruleResult = 'rule' in value
            ? evaluate(value, prerequisites)
            : prerequisites.some(prerequisite => {
                return prerequisite.subject === value.subject &&
                    prerequisite.course_number === value.course_number &&
                        prerequisite.test === value.test;
            });

        if(logic.rule === 'AND') {
            result = result && ruleResult;
        } else {
            if(ruleResult) return true;
            result = false;
        }
    }

    return result;
}