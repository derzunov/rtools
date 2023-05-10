export const calculateCampaignPeriodInDays = ( startTimestamp, endTimestamp ) => {

    if ( endTimestamp < startTimestamp ) {
        return 0;
    }

    const periodMs = endTimestamp - startTimestamp
    const periodInDays = ( periodMs / 1000 / 60 / 60 / 24 ) + 1
    return periodInDays
}
