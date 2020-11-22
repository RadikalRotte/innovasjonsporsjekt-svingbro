#include <TheThingsNetwork.h>
#include <LiquidCrystal.h>

const int pinHall = A0;
const int buttonPin = 8;
const int ledGreen = 13;
const int ledRed = 12;
const int ledYellow = 11;
LiquidCrystal lcd(10, 9, 5, 4, 3, 2);

int magnet = 0;
int button = 0;

//for å koble seg på TTN / LoRaWAN
const char *appEui = "70B3D57ED0036C5B";
const char *appKey = "7B2C4AAE6F3FE6179A0552718F382BD9";

#define loraSerial Serial1
#define debugSerial Serial

#define freqPlan TTN_FP_EU868

TheThingsNetwork ttn(loraSerial, debugSerial, freqPlan);

int led(String(colour)){
  if(colour == "red"){
    digitalWrite(ledRed, HIGH);
    digitalWrite(ledGreen, LOW);
    digitalWrite(ledYellow, LOW);
    lcd.print("Svingbrua er");
    lcd.setCursor(0,1); 
    lcd.print("ikke gangbar");
    lcd.setCursor(0,0);
    return 9;}
  else if(colour == "green"){
    digitalWrite(ledRed, LOW);
    digitalWrite(ledGreen, HIGH);
    digitalWrite(ledYellow, LOW);
    lcd.print("Svingbrua er");
    lcd.setCursor(0,1); 
    lcd.print("gangbar");
    lcd.setCursor(0,0);
    return 17;}
  else if(colour == "yellow"){
    digitalWrite(ledRed, LOW);
    digitalWrite(ledGreen, LOW);
    digitalWrite(ledYellow, HIGH);}
    }

int hoved = 17;
int midlertidig = 9;

void setup() {
  loraSerial.begin(57600);
  debugSerial.begin(9600);

  while (!debugSerial && millis() < 10000);

  debugSerial.println("Status");
  ttn.showStatus();

  debugSerial.println("Join");
  ttn.join(appEui, appKey);

  pinMode(pinHall, INPUT);
  pinMode(buttonPin, INPUT);
  pinMode(ledGreen, OUTPUT);
  pinMode(ledRed, OUTPUT);
  pinMode(ledYellow, OUTPUT);
  
  lcd.begin(16,2);

  Serial.begin(9600);
}

void loop() {
  //data som skal sendes til the things network
  byte payload[2];
  payload[0] = magnet;
  payload[1] = button;
  
  if( (millis() % 30000) == 0){ //sender data til TTN hvert halve minutt
    ttn.sendBytes(payload, sizeof(payload));
  }

   if(analogRead(pinHall) >= 400){
     magnet = 0;}
   else{
     magnet = 1;}

  //sjekker om systemet har opplevd en endring
  if(midlertidig != hoved){
    lcd.clear();
    byte payload[2];
    payload[0] = magnet;
    payload[1] = button;
    ttn.sendBytes(payload, sizeof(payload));}
  
  midlertidig = hoved;
  
   //knappen trykkes på
   if(digitalRead(buttonPin)==HIGH){
     int minutter = 10;
     button = 1;
     byte payload[2];
     payload[0] = magnet;
     payload[1] = button;
     ttn.sendBytes(payload, sizeof(payload));
     led("yellow");
     lcd.clear();
     lcd.print("Brua stenger om");
     lcd.setCursor(0,1);
     lcd.print("ca.");
     lcd.print(minutter);
     lcd.print(" minutter");
     while(analogRead(pinHall)<400){
      //loopen kan avbrytes ved å trykke på knappen igjen
      if(digitalRead(buttonPin)==HIGH){
        button = 0;
        byte payload[2];
        payload[0] = magnet;
        payload[1] = button;
        ttn.sendBytes(payload, sizeof(payload));
        lcd.clear();
        led("green");
        break;
       }
      //nedtellingen begynner her 
      if( (millis() % 60000) < 550){
       ttn.sendBytes(payload, sizeof(payload));
       lcd.clear();
       lcd.print("Brua stenger om");
       lcd.setCursor(0,1);
       lcd.print("ca.");
       lcd.print(minutter);
       lcd.print(" minutter");
       lcd.setCursor(0,0);
       if(minutter>0){
         minutter -= 1;}
       else{
         minutter = 0;}
       }
       }
    lcd.clear();}
  
  //grønt når magneten er inntil, rødt hvis ikke.
  else{
    button = 0; 
    if(analogRead(pinHall)<400){ 
      hoved = led("green");}
    else if(analogRead(pinHall)>=400){ 
      hoved = led("red");}
      }
}
