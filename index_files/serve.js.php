(function () {
                
    var revDiv = document.querySelector('script[id="rc_160"]').parentNode;

    var usPrivacy, ccpaDone = false;
    var gdpr, gdprConsent, gdprDone = false;

            //IAB's CCPA function
        if (typeof __uspapi === 'function') {
            __uspapi('getUSPData', 1, function(uspData, success) {
                if(success) {
                    usPrivacy = uspData.uspString;
                }
                ccpaDone = true;
            });
        } else {
            ccpaDone = true;
        }
    
            //IAB's GDPR function
        var gdpr, gdprConsent, gdprDone = false;
        if (typeof __tcfapi === 'function') {
            __tcfapi('getTCData', 2, function(tcData, success) {
                if (success) {
                    gdpr = tcData.gdprApplies === true ? 1 : 0;
                    gdprConsent = tcData.tcString;
                }
                gdprDone = true;
            }, [203,210,185,277,32,128,344,52,165,14,81,341,599]);
        } else if (typeof __cmp === 'function') {
            __cmp('getConsentData', null, function(vendorConsentData, success) {
                if (success) {
                    gdpr = vendorConsentData.gdprApplies === true  ? 1 : 0;
                    gdprConsent = vendorConsentData.consentData;
                }
                gdprDone = true;
            });
        } else {
            gdprDone = true;
        }
    
    var appendRcel = function(url) {
        var rcel = document.createElement("script");
        rcel.id = 'rc_' + Math.floor(Math.random() * 1000);
        rcel.type = 'text/javascript';
        rcel.src = url;
        rcel.async = true;
        revDiv.appendChild(rcel);
    }

    var callRev_rc_160 = function() {
        var url = 'https://trends.revcontent.com/serve.js.php?w=90320&t=rc_160&c=1601478955464&width=1536';
        if(gdpr != null) url += '&gdpr=' + gdpr;
        if(gdprConsent != null) url += '&gdpr_consent=' + gdprConsent;
        if(usPrivacy != null) url += '&us_privacy=' + usPrivacy;

        var siteUrl = '';
        if (window.location) {
            siteUrl = window.location.href;
        }
        url += '&site_url=' + encodeURIComponent(siteUrl.substr(0,700));
        url += '&referer=' + encodeURIComponent(document.referrer.substr(0,700));
        url += '&skip_iab=true'

        try {
            var rcxhr = new XMLHttpRequest();
            rcxhr.onreadystatechange = function() {
                if (rcxhr.readyState == 4) {
                    var rcel = document.createElement("script");
                    rcel.type = 'text/javascript';
                    rcel.text = rcxhr.responseText;
                    rcel.async = true;
                    revDiv.appendChild(rcel);
                }
            }
            rcxhr.open("POST", url, true);
            rcxhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                        rcxhr.withCredentials = true;
                        rcxhr.send();

            rcxhr.onerror = function() {
                appendRcel(url);
            }
        } catch(e) {
            appendRcel(url);
        }
    };

            //50ms interval check if IAB CCPA/GDPR finished
        var gdprCcpaIntervalCheck = setInterval(function() {
            if(ccpaDone && gdprDone) {
                clearTimeout(gdprCcpaTimeoutCheck);
                clearInterval(gdprCcpaIntervalCheck);
                callRev_rc_160();
            }
        }, 30);

        //200ms timeout for IAB CCPA/GDPR functions to finish
        var gdprCcpaTimeoutCheck = setTimeout(function() {
            clearInterval(gdprCcpaIntervalCheck);
            callRev_rc_160();
        }, 200);
    
})();
