const Ziggy = {"url":"https:\/\/vault.marceli.to.test","port":null,"defaults":{},"routes":{"dashboard":{"uri":"dashboard","methods":["GET","HEAD"]},"entries.store":{"uri":"entries","methods":["POST"]},"entries.update":{"uri":"entries\/{entry}","methods":["PUT"],"parameters":["entry"],"bindings":{"entry":"id"}},"entries.destroy":{"uri":"entries\/{entry}","methods":["DELETE"],"parameters":["entry"],"bindings":{"entry":"id"}},"tasks.index":{"uri":"tasks","methods":["GET","HEAD"]},"tasks.store":{"uri":"tasks","methods":["POST"]},"tasks.update":{"uri":"tasks\/{task}","methods":["PUT"],"parameters":["task"],"bindings":{"task":"id"}},"tasks.destroy":{"uri":"tasks\/{task}","methods":["DELETE"],"parameters":["task"],"bindings":{"task":"id"}},"login":{"uri":"login","methods":["GET","HEAD"]},"logout":{"uri":"logout","methods":["POST"]}}};
if (typeof window !== 'undefined' && typeof window.Ziggy !== 'undefined') {
  Object.assign(Ziggy.routes, window.Ziggy.routes);
}
export { Ziggy };
