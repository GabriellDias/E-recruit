// server.js
//Author: Samuel de Souza Silva

const
  bodyParser = require('body-parser'),
  //config = require('config'),
  crypto = require('crypto'),
  express = require('express'),
  https = require('https'),
  request = require('request');

var vagaInteracao = "";
var ultQuestion = 0;

// init project
var app = express();
app.use(bodyParser.json({ verify: verifyRequestSignature }));
app.use(express.static('public'));

const APP_SECRET = process.env.MESSENGER_APP_SECRET;/*(process.env.MESSENGER_APP_SECRET) ?
  process.env.MESSENGER_APP_SECRET :
  config.get('appSecret');*/

const VALIDATION_TOKEN = process.env.MESSENGER_VALIDATION_TOKEN;/*(process.env.MESSENGER_VALIDATION_TOKEN) ?
  (process.env.MESSENGER_VALIDATION_TOKEN) :
  config.get('validationToken');*/

const PAGE_ACCESS_TOKEN = process.env.MESSENGER_PAGE_ACCESS_TOKEN;/*(process.env.MESSENGER_PAGE_ACCESS_TOKEN) ?
  (process.env.MESSENGER_PAGE_ACCESS_TOKEN) :
  config.get('pageAccessToken');*/


if (!(APP_SECRET && VALIDATION_TOKEN && PAGE_ACCESS_TOKEN)) {
  console.error("Missing config values");
  process.exit(1);
}


/*
 * Verify that the callback came from Facebook. Using the App Secret from
 * the App Dashboard, we can verify the signature that is sent with each
 * callback in the x-hub-signature field, located in the header.
 *
 * https://developers.facebook.com/docs/graph-api/webhooks#setup
 *
 */
function verifyRequestSignature(req, res, buf) {
  var signature = req.headers["x-hub-signature"];


  if (!signature) {
    // For testing, let's log an error. In production, you should throw an
    // error.
    console.error("Couldn't validate the signature.");
  } else {
    var elements = signature.split('=');
    var method = elements[0];
    var signatureHash = elements[1];

    var expectedHash = crypto.createHmac('sha1', APP_SECRET)
                        .update(buf)
                        .digest('hex');

    if (signatureHash != expectedHash) {
      throw new Error("Couldn't validate the request signature.");
    }
  }
}

// http://expressjs.com/en/starter/basic-routing.html
app.get("/", function (request, response) {
  response.sendFile(__dirname + '/views/index.html');
});

app.get("/webhook", function (request, response) {
  if (request.query['hub.mode'] === 'subscribe' && request.query['hub.verify_token'] === VALIDATION_TOKEN) {
    response.status(200).send(request.query['hub.challenge']);
  } else {
    console.error("Failed validation. Make sure the validation tokens match.");
    response.sendStatus(403);
  }
});

app.post('/webhook', function (req, res) {
  var data = req.body;

  // Make sure this is a page subscription
  if (data.object == 'page') {
    // Iterate over each entry
    // There may be multiple if batched
    data.entry.forEach(function(pageEntry) {
      var pageID = pageEntry.id;
      var timeOfEvent = pageEntry.time;

      // Iterate over each messaging event
      pageEntry.messaging.forEach(function(messagingEvent) {
        if (messagingEvent.message) {
          receivedMessage(messagingEvent);
        } else if (messagingEvent.postback) {
          receivedPostback(messagingEvent);
        } else {
          console.log("Webhook received unknown messagingEvent: ", messagingEvent);
        }
      });
    });

    // Assume all went well.
    //
    // You must send back a 200, within 20 seconds, to let us know you've
    // successfully received the callback. Otherwise, the request will time out.
    res.sendStatus(200);
  }
});

function receivedMessage(event) {
  var senderID = event.sender.id;
  var recipientID = event.recipient.id;
  var timeOfMessage = event.timestamp;
  var message = event.message;

  console.log("Received message for user %d and page %d at %d with message:",
    senderID, recipientID, timeOfMessage);
  console.log(JSON.stringify(message));

  var isEcho = message.is_echo;
  var messageId = message.mid;
  var appId = message.app_id;
  var metadata = message.metadata;

  // You may get a text or attachment but not both
  var messageText = message.text;
  var messageAttachments = message.attachments;
  var quickReply = message.quick_reply;

  if (isEcho) {
    // Just logging message echoes to console
    console.log("Received echo for message %s and app %d with metadata %s",
      messageId, appId, metadata);
    return;
  }else if (quickReply) {
    var quickReplyPayload = quickReply.payload;
    console.log("Quick reply for message %s with payload %s",
      messageId, quickReplyPayload);

    if(quickReplyPayload === "area-interest-yes"){
      loadAreas(event);
    }else if(quickReplyPayload === "area-interest-no"){
      sendTextMessage(senderID, "Obrigado pelo Contato!");
    }

    return;
  }

  if (messageText) {

    saveAnswer(event);

  } else if (messageAttachments) {
    event.message.text = message.attachments[0].payload.url;
    saveAnswer(event);
    sendTextMessage(senderID, "Arquivo recebido!");
  }
}

/*
 * Send a text message using the Send API.
 *
 */
function sendTextMessage(recipientId, messageText) {
  var messageData = {
    recipient: {
      id: recipientId
    },
    message: {
      text: messageText,
      metadata: "DEVELOPER_DEFINED_METADATA"
    }
  };

  callSendAPI(messageData);
}


/*
 * Call the Send API. The message data goes in the body. If successful, we'll
 * get the message id in a response
 *
 */
function callSendAPI(messageData) {
  request({
    uri: 'https://graph.facebook.com/v2.6/me/messages',
    qs: { access_token: PAGE_ACCESS_TOKEN },
    method: 'POST',
    json: messageData

  }, function (error, response, body) {
    if (!error && response.statusCode == 200) {
      var recipientId = body.recipient_id;
      var messageId = body.message_id;

      if (messageId) {
        console.log("Successfully sent message with id %s to recipient %s",
          messageId, recipientId);
      } else {
      console.log("Successfully called Send API for recipient %s",
        recipientId);
      }
    } else {
      console.error("Failed calling Send API", response.statusCode, response.statusMessage, body.error);
    }
  });
}

function receivedPostback(event) {
  var senderID = event.sender.id;
  var recipientID = event.recipient.id;
  var timeOfPostback = event.timestamp;

  // The 'payload' param is a developer-defined field which is set in a postback
  // button for Structured Messages.
  var payload = event.postback.payload;

  if(payload.indexOf("show-vagas") >= 0){
    loadVagas(event);
    if(payload.indexOf("start-chat") >= 0){
      getInfoUser(event);
    }
  }else if(payload.indexOf("show-areas") >= 0){
    loadAreas(event);
  }else if(payload.indexOf("pre-ent") >= 0){
    startInterview(event);
  }else if(payload.indexOf("area-interest") >= 0){
    saveInterest(event);
  }

  console.log("Received postback for user %d and page %d with payload '%s' " +
    "at %d", senderID, recipientID, payload, timeOfPostback);

}

function getInfoUser(event){
  request('https://graph.facebook.com/v2.6/' + event.sender.id + '?fields=first_name,last_name,profile_pic,locale,timezone,gender&access_token=' + PAGE_ACCESS_TOKEN, function (error, response, body) {
    if (!error && response.statusCode == 200) {
      var user = JSON.parse(body);
      user.id = event.sender.id;
      saveInfoUser(user);
    }
  });
}

function getNextQuestion(user_id, vaga_id){
  request('http://www.codeside.com.br/apibotrecruit/entrevista/nextquestion/' + user_id + '/' + vaga_id, function (error, response, body) {
    if (!error && response.statusCode == 200) {
      var question = JSON.parse(body);
      console.log("retorno nextquestion: " + question.retorno.has);
      if(question.retorno.has == 1){
        //envia questão
        if(question.retorno.start == 1){
          sendTextMessage(user_id, "Certo! Vamos bater um papo!\n" + question.retorno.texto);
        }else{
          sendTextMessage(user_id, question.retorno.texto);
        }

        setQuestionSent(question.retorno.id, question.retorno.entrevista);
        setEntrevistaAtual(user_id, question.retorno.entrevista);
      }else if(question.retorno.finish == 1){
        sendTextMessage(user_id, "A Entrevista foi finalizada! Obrigado por se candidatar! Nossa equipe entrará em contato!");
      }else{
        newInterview(user_id, vaga_id);
      }
    }else{
      console.log("errorrr: " + error + response);
    }
  });
}

function setQuestionSent(question_id, entrevista_id){

  var question = {
    "question_id" : question_id,
    "entrevista_id" : entrevista_id
  };

  request({
    uri: 'http://www.codeside.com.br/apibotrecruit/pergunta/setsent',
    method: 'POST',
    json: question

  }, function (error, response, body) {
    if (error || response.statusCode != 200) {
      console.error("Failed setQuestionSent" + " - Status code: " + response.statusCode + " - StatusMessage: " + response.statusMessage);
    }
  });
}

function saveInfoUser(user){

  request({
    uri: 'http://www.codeside.com.br/apibotrecruit/usuario/botadd',
    method: 'POST',
    json: user

  }, function (error, response, body) {
    if (error || response.statusCode != 200) {
      console.error("Failed save info of user" + " - Status code: " + response.statusCode + " - StatusMessage: " + response.statusMessage);
    }else{
      console.log(body);
    }
  });
}

function newInterview(user_id, vaga_id){
  var entrevista = {
    "user_id": user_id,
    "vaga_id": vaga_id
  };

  request({
    uri: 'http://www.codeside.com.br/apibotrecruit/entrevista/botadd',
    method: 'POST',
    json: entrevista

  }, function (error, response, body) {
    if (error || response.statusCode != 200) {
      console.error("Failed on save new interview" + " - Status code: " + response.statusCode + " - StatusMessage: " + response.statusMessage);
      sendTextMessage(user_id, "Houve um problema na entrevista. Por favor tente mais tarde.");
    }else{
      getNextQuestion(user_id, vaga_id);
    }
  });
}

function sendAreas(senderID, areasList){
  if(areasList.length > 0){
    var areas = {
              "recipient":{
                "id": senderID
              },
              "message":{
                "attachment":{
                  "type":"template",
                  "payload":{
                    "template_type":"generic",
                    "elements":areasList
                  }
                }
              }
            };

   sendTextMessage(senderID, "Selecione uma área de seu interesse.");
   callSendAPI(areas);
 }else{//Se não houver áreas, aguarda resposta do administrador da página
   sendTextMessage(senderID, "Olá, No momento não possuimos vagas. Deixe sua mensagem que em breve entraremos em contato!");
 }
}

function loadAreas(event){

  request('http://www.codeside.com.br/apibotrecruit/area/showall', function (error, response, body) {
    if (!error && response.statusCode == 200) {
      var consult = JSON.parse(body);
      var areasList = new Array();

      consult.areas.forEach(function(area) {
        console.log("exib area: " + area.title );
        areasList.push({
                          "title": area.title,
                          "item_url":"https://facebook.com",
                          "image_url": area.image_url,
                          "subtitle": area.subtitle,
                          "buttons":[
                            {
                              "type":"postback",
                              "title":"Tenho interesse!",
                              "payload":"area-interest-" + area.id
                            }
                          ]
        });
      });
      sendAreas(event.sender.id, areasList);
    }
  });
}

function sendVagas(senderID, vagasList){
  if(vagasList.length > 0){
    var vagas = {
               "recipient":{
                 "id": senderID
               },
               "message":{
                 "attachment":{
                   "type":"template",
                   "payload":{
                     "template_type":"generic",
                     "elements":vagasList
                   }
                 }
               }
             };

     sendTextMessage(senderID, "Estas são nossas vagas abertas.");

     callSendAPI(vagas);
   }else{

     var questionInterest = {
                              "recipient":{
                                "id":senderID
                              },
                              "message":{
                                "text":"No momento não possuimos vagas. Você tem interesse em acompanhar a abertura de vagas?",
                                "quick_replies":[
                                  {
                                    "content_type":"text",
                                    "title":"Sim",
                                    "payload":"area-interest-yes"
                                  },
                                  {
                                    "content_type":"text",
                                    "title":"Não",
                                    "payload":"area-interest-no"
                                  }
                                ]
                              }
                            };
     callSendAPI(questionInterest);
  }
}

function loadVagas(event){

  request('http://www.codeside.com.br/apibotrecruit/vaga/showall', function (error, response, body) {
    if (!error && response.statusCode == 200) {
      var consult = JSON.parse(body);
      var vagasList = new Array();

      consult.vagas.forEach(function(vaga) {
        console.log("exib vaga: " + vaga.title );
        vagasList.push({
                          "title": vaga.title,
                          "item_url":vaga.link_web,
                          "image_url": vaga.image_url,
                          "subtitle": vaga.subtitle,
                          "buttons":[
                            {
                              "type":"web_url",
                              "url":vaga.link_web,
                              "title":"Detalhes",
                              "webview_height_ratio": "compact"
                            },
                            {
                              "type":"postback",
                              "title":"Fazer Pré Entrevista",
                              "payload":"pre-ent-" + vaga.id
                            }
                          ]
        });
      });
      sendVagas(event.sender.id, vagasList);
    }
  });
}

function saveInterest(event){
  var payload = event.postback.payload;
  var senderID = event.sender.id;

  var area = payload.substr(14, payload.length-14);

  sendTextMessage(senderID, "Área de interesse registrada! Te notificaremos ao abrirmos uma vaga nesta área!");
}


function saveAnswer(event){
  var msg = event.message.text;
  var senderID = event.sender.id;

  var message = {
    "texto": msg,
    "user_id": senderID
  };

  request({
    uri: 'http://www.codeside.com.br/apibotrecruit/pergunta/resposta',
    method: 'POST',
    json: message

  }, function (error, response, body) {
    if (error || response.statusCode != 200) {
      console.error("Failed on save new interview" + " - Status code: " + response.statusCode + " - StatusMessage: " + response.statusMessage);
      sendTextMessage(message.user_id, "Houve um problema na entrevista. Por favor tente mais tarde.");
    }else{
      console.log("saveAnswer  " + body.result);
        console.log("typeof: " + !isNaN(body.result));
      if(!isNaN(body.result)){
        if(body.result > 0){
          getNextQuestion(senderID, body.result);
        }
      }
    }
  });

}

function startInterview(event){
  var payload = event.postback.payload;
  var senderID = event.sender.id;
  var vaga = payload.substr(8, payload.length-8);

   getNextQuestion(senderID, vaga);
  //busca questao pendente de resposta para a vaga
}

function setEntrevistaAtual(user_id, entrevista_id){
  var message = {
    "entrevista_id": entrevista_id,
    "user_id": user_id
  };

  console.log("entrevista: " + entrevista_id + "  user: " + user_id);

  request({
    uri: 'http://www.codeside.com.br/apibotrecruit/usuario/setpreent',
    method: 'POST',
    json: message

  }, function (error, response, body) {
    if (error || response.statusCode != 200) {
      console.error("Failed on set Interview" + " - Status code: " + response.statusCode + " - StatusMessage: " + response.statusMessage);
      sendTextMessage(user_id, "Houve um problema na entrevista. Por favor tente mais tarde.");
    }
  });
}

// listen for requests :)
var listener = app.listen(process.env.PORT, function () {
  //console.log('Your app is listening on port ' + listener.address().port);
});
