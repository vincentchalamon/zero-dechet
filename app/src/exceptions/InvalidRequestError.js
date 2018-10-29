export default class InvalidRequestError {
    violations = {};

    constructor(violations) {
        this.violations = violations;
    }
}
