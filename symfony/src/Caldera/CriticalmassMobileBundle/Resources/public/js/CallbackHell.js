CallbackHell = function()
{

};

CallbackHell.eventListeners = new Array();
CallbackHell.oneTimeEventListeners = new Array();

CallbackHell.registerEventListener = function(eventName, callbackFunction)
{
    if (this.eventListeners[eventName] == null)
    {
        this.eventListeners[eventName] = new Array();
    }

    this.eventListeners[eventName].push(callbackFunction);
};

CallbackHell.registerOneTimeEventListener = function(eventName, callbackFunction)
{
    if (this.oneTimeEventListeners[eventName] == null)
    {
        this.oneTimeEventListeners[eventName] = new Array();
    }

    this.oneTimeEventListeners[eventName].push(callbackFunction);
};

CallbackHell.executeEventListener = function(eventName, argument)
{
    if (this.oneTimeEventListeners[eventName] != null)
    {
        var callbackFunction;

        for (callbackFunction in this.eventListeners[eventName])
        {
            this.eventListeners[eventName][callbackFunction](argument);
        }

        while (callbackFunction = this.oneTimeEventListeners[eventName].pop())
        {
            callbackFunction(argument);
        }
    }
};