/**
 * Events
 *
 * A _very_ simple event manager.
 */

/**
 * Events provides a VERY simple event manager, with just the minimum needed to support this app.
 *
 * @param eventSet : optional, pass an array of event types and the class will throw if an unknown event is
 * mentioned, useful for catching typos in event names
 * @constructor
 */
class Events {
  constructor(eventSet) {
    this.eventSet = eventSet;
    this.eventHandlers = {};
  }

  /**
   * Register for an event
   *
   * @param event    : the event (string) to listen for
   * @param callback : function to call when event is triggered, args will be whatever is passed to trigger
   * @param context  : optional, context for callback function
   */
  on(event, callback, context) {
    if (this.eventSet && this.eventSet.indexOf(event) === -1) {
      throw "Unknown event: " + event;
    }
    const handlers = this.eventHandlers[event] || (this.eventHandlers[event] = []);
    handlers.push({
      callback: callback,
      context: context
    });
  }

  /**
   * Unregister for (an) event(s)
   *
   * @param event    : event (string) to stop listening, or undefined to match all events
   * @param callback : function to remove, or undefined to match all functions
   * @param context  : context to remove, optional
   *
   * Note: implication is that calling off() with no args removes all handlers for all events
   */
  off(event, callback, context) {

    function filterCallback(element) {
      return (callback && callback != element.callback) || (context && context != element.context);
    }

    for (const evt in this.eventHandlers) {
      if (!event || event == evt) {
        this.eventHandlers[evt] = this.eventHandlers[evt].filter(filterCallback);
      }
    }

  }

  /**
   * Triggers an event, calling all the handlers
   *
   * @param event : the event (string) to trigger
   * @param args  : any/all remaining arguments to trigger will be passed to the handlers
   */
  trigger(event, args) {
    if (this.eventSet && this.eventSet.indexOf(event) === -1) {
      throw "Unknown event: " + event;
    }

    const handlers = this.eventHandlers[event];
    if (handlers) {
      for (var i = 0; i < handlers.length; i++) {
        handlers[i].callback.apply(handlers[i].context, Array.prototype.slice.call(arguments, 1));
      }
    }
  }
}

const events = new Events();

export default events;
