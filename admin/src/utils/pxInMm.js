import { default as tracer } from '../../../../../vue_project/stikers/src/utils/tracer';

export function convertPxToMm(variable, canvasHeightInPx) {
    tracer.debug('pxInMm called');
    const maxHeightInMm = 297;
    let pxInMm = 96 / 25.4;
    let maxHeightInPx = maxHeightInMm * pxInMm;
    let factorVar = variable / pxInMm
    let variableCm = variable / factorVar * 50;
    let factor = maxHeightInPx / canvasHeightInPx;
    return Math.ceil(variableCm / factor);
}