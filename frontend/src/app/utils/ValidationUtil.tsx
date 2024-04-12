export const validateRequiredFields = (inputData: string | null, fieldName: string): void => {
    if (inputData == null) {
        throw new Error("null is not allowed.");
    }
    if (inputData == undefined) {
        throw new Error("undefined is not allowed.");
    }
    if (inputData == "") {
        throw new Error("Empty string is not allowed.");
    }
};




