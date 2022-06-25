package program_code;
import java.io.Serializable;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.lang.Exception;

public class Reservation implements Serializable {
    private Person person;
    private Hotel hotel;
    private Room room;
    private ArrayList<Offer> offers;
    private boolean reserved, payed, withCard;
    private Date dateFrom, dateTill, datePayment;

    public Reservation(Person person, Hotel hotel, Room room, boolean reserved, Date dateFrom, Date dateTill) {
        this.person = person;
        this.hotel = hotel;
        this.room = room;
        this.reserved = reserved;
        this.payed = false;
        this.dateFrom = dateFrom;
        this.dateTill = dateTill;
        this.offers = new ArrayList<Offer>();
    }

    public Reservation(Person person, Hotel hotel, Room room, ArrayList<Offer> offers, boolean payed, boolean withCard, Date dateFrom, Date dateTill, Date datePayment) {
        this.person = person;
        this.hotel = hotel;
        this.room = room;
        this.offers = offers;
        this.reserved = false;
        this.payed = payed;
        this.withCard = withCard;
        this.dateFrom = dateFrom;
        this.dateTill = dateTill;
        this.datePayment = datePayment;
    }
    
     public boolean isWithCard() {
        return withCard;
    }

    public void setWithCard(boolean withCard) {
        this.withCard = withCard;
    }

    public Date getDatePayment() {
        return datePayment;
    }

    public void setDatePayment(Date datePayment) {
        this.datePayment = datePayment;
    }   
    
    public ArrayList<Offer> getOffers() {
        return offers;
    }

    public void setOffers(ArrayList<Offer> offers) {
        this.offers = offers;
    }

    public Person getPerson() {
        return person;
    }

    public void setPerson(Person person) {
        this.person = person;
    }

    public Hotel getHotel() {
        return hotel;
    }

    public void setHotel(Hotel hotel) {
        this.hotel = hotel;
    }

    public Room getRoom() {
        return room;
    }

    public void setRoom(Room room) {
        this.room = room;
    }

    public boolean isReserved() {
        return reserved;
    }

    public void setReserved(boolean reserved) {
        this.reserved = reserved;
    }

    public boolean isPayed() {
        return payed;
    }

    public void setPayed(boolean payed) {
        this.payed = payed;
    }

    public Date getDateFrom() {
        return dateFrom;
    }

    public void setDateFrom(Date dateFrom) {
        this.dateFrom = dateFrom;
    }

    public Date getDateTill() {
        return dateTill;
    }

    public void setDateTill(Date dateTill) {
        this.dateTill = dateTill;
    }
    
    public String toString(){
         String information = this.person.getName() + " - " + this.hotel.getName();
         
         DateFormat dateFormat = new SimpleDateFormat("dd-MM-yyyy");  
         String strDateFrom = dateFormat.format(this.dateFrom);
         String strDateTill = dateFormat.format(this.dateTill);
         String strDatePayment = "";
         try{strDatePayment = dateFormat.format(this.datePayment);}
         catch (Exception ex){}
         
         information += " ( room № " + this.room.getNumber() + " from ";
         information += strDateFrom + " till " + strDateTill + " )";
         
         long timeDiff = this.getDateTill().getTime() - this.getDateFrom().getTime();
         int duration = (int) (timeDiff / (1000 * 60 * 60* 24));
         Float sum = this.room.getCategory().getPricePerDay() * duration;
         if(duration > this.room.getCondition()){sum = sum * (1-0.01f*this.room.getDiscount());}
         
         if (offers.size()>0)
            {information += " - offers:";
            for(Offer o: this.offers)
                {information += " " + o.getName();
                sum += o.getPrice();}}
         else {information += " - without offers";}
         
         information += " - total sum: " + String.valueOf(sum) + this.room.getCategory().getCurrency();
         
         information += " - status: ";
         if(this.reserved) {information += "reserved, ";}
         else {information += "wasn't reserved, ";}
         if(this.payed) 
            {information += "payed ( ";
                if(this.withCard){information += "with card - ";}
                else {information += "with cash - ";}
                information += strDatePayment + " )";
            }
         else {information += "wasn't payed";}
         
         return information;
    }
}
