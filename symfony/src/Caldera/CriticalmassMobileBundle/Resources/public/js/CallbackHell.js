CallbackHell = function()
{

};

CallbackHell.eventListeners = new Array();
CallbackHell.oneTimeEventListeners = new Array();
CallbackHell.firedEventListeners = new Array();

CallbackHell.registerEventListener = function(eventName, callbackFunction)
{
    if (this.eventListeners[eventName] == null)
    {
        this.eventListeners[eventName] = new Array();
    }

    this.eventListeners[eventName].push(callbackFunction);
    this.initFiredEventListenerCounter(eventName);
};

CallbackHell.registerOneTimeEventListener = function(eventName, callbackFunction)
{
    if (this.oneTimeEventListeners[eventName] == null)
    {
        this.oneTimeEventListeners[eventName] = new Array();
    }

    this.oneTimeEventListeners[eventName].push(callbackFunction);
    this.initFiredEventListenerCounter(eventName);
};

CallbackHell.executeEventListener = function(eventName, argument)
{
    if (this.oneTimeEventListeners[eventName] != null)
    {
        this.countFiredEventListener(eventName);

        var callbackFunction = null;

        while (callbackFunction = this.oneTimeEventListeners[eventName].pop())
        {
            callbackFunction(argument);
        }
    }

    if (this.eventListeners[eventName] != null)
    {
        this.countFiredEventListener(eventName);

        for (var callbackFunction in this.eventListeners[eventName])
        {
            this.eventListeners[eventName][callbackFunction](argument);
        }
    }
};

CallbackHell.initFiredEventListenerCounter = function(eventName)
{
    this.firedEventListeners[eventName] = 0;
};

CallbackHell.countFiredEventListener = function(eventName)
{
    if (this.firedEventListeners[eventName])
    {
        ++this.firedEventListeners[eventName];
    }
    else
    {
        this.firedEventListeners[eventName] = 1;
    }
};

CallbackHell.hasEventListenerBeenFired = function(eventName)
{
    return this.firedEventListeners[eventName] != null && this.firedEventListeners[eventName] > 0;
};