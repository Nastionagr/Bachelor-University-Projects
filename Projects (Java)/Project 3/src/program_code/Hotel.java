package program_code;
import java.io.File;
import java.io.Serializable;
import java.util.ArrayList;
import java.util.Date;

public class Hotel implements Serializable {
    private String name, country, address, owner, email;
    private ArrayList<File> photos;
    private ArrayList<Category> categories;
    private ArrayList<Offer> offers;
    private ArrayList<Room> rooms;

    public Hotel(String name, String country, String address, String owner, String email, ArrayList<File> photos, ArrayList<Category> categories, ArrayList<Offer> offers, ArrayList<Room> rooms) {
        this.name = name;
        this.country = country;
        this.address = address;
        this.owner = owner;
        this.email = email;
        this.photos = photos;
        this.categories = categories;
        this.offers = offers;
        this.rooms = rooms;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public String getCountry() {
        return country;
    }

    public void setCountry(String country) {
        this.country = country;
    }

    public String getAddress() {
        return address;
    }

    public void setAddress(String address) {
        this.address = address;
    }

    public String getOwner() {
        return owner;
    }

    public void setOwner(String owner) {
        this.owner = owner;
    }

    public String getEmail() {
        return email;
    }

    public void setEmail(String email) {
        this.email = email;
    }

    public ArrayList<File> getPhotos() {
        return photos;
    }

    public void setPhotos(ArrayList<File> photos) {
        this.photos = photos;
    }

    public ArrayList<Category> getCategories() {
        return categories;
    }

    public void setCategories(ArrayList<Category> categories) {
        this.categories = categories;
    }

    public ArrayList<Offer> getOffers() {
        return offers;
    }

    public void setOffers(ArrayList<Offer> offers) {
        this.offers = offers;
    }

    public ArrayList<Room> getRooms() {
        return rooms;
    }

    public void setRooms(ArrayList<Room> rooms) {
        this.rooms = rooms;
    }
  
    public String toString()
    {
         return this.name + "  (owner: " + this.owner + ")";
    }
    
    public ArrayList<Reservation> seeAllReservations(ArrayList<Reservation> reservations)
    {
        ArrayList<Reservation> history = new ArrayList<Reservation>();
        
        for(Reservation r : reservations){
            if(this.equals(r.getHotel()))
                {history.add(r);}}
        
        return history;
    }
    
    public void seeAvailableRooms(ArrayList<Room> availableRooms, ArrayList<Reservation> reservations, Date dateFrom, Date dateTill)
    {        
        for(Room rm : this.rooms)
        {
            boolean isAvailable = true;

            for(Reservation rs : reservations)
                {
                    boolean reserved = false;                    
                    if(dateFrom.after(rs.getDateFrom()) && dateFrom.before(rs.getDateTill())) {reserved = true;}
                    if(dateTill.after(rs.getDateFrom()) && dateTill.before(rs.getDateTill())) {reserved = true;}
                            
                    if (rm.equals(rs.getRoom()) && reserved){isAvailable = false;}
                }
            
            if(isAvailable){availableRooms.add(rm);}            
        }
    }
}