/**
 * Post Views Analytics - Asynchronous REST Tracking Client
 */
document.addEventListener('DOMContentLoaded', function() {
    if (typeof pvaTrackerEndpoint === 'undefined' || !pvaTrackerEndpoint.root) {
        return;
    }
    
    // Non-blocking fetch routing completely evades upstream static HTML caches
    fetch(pvaTrackerEndpoint.root + 'post-views-analytics/v1/track', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': pvaTrackerEndpoint.nonce
        },
        body: JSON.stringify({
            post_id: pvaTrackerEndpoint.post_id
        })
    })
    .then(function(response) {
        return response.json();
    })
    .catch(function(err) {
        console.warn('PVA Async routing event anomaly:', err);
    });
});