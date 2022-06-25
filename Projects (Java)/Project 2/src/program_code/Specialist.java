package program_code;

public class Specialist {
    private String name, education, certificates;
    private Float pricePerDay;
    private int yearExperience, monthExperience;
    private boolean available;

    public Specialist(String name, String education, String certificates, Float pricePerDay, int yearExperience, int monthExperience) {
        this.name = name;
        this.education = education;
        this.certificates = certificates;
        this.pricePerDay = pricePerDay;
        this.yearExperience = yearExperience;
        this.monthExperience = monthExperience;
        this.available = true;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public String getEducation() {
        return education;
    }

    public void setEducation(String education) {
        this.education = education;
    }

    public String getCertificates() {
        return certificates;
    }

    public void setCertificates(String certificates) {
        this.certificates = certificates;
    }

    public Float getPricePerDay() {
        return pricePerDay;
    }

    public void setPricePerDay(Float pricePerDay) {
        this.pricePerDay = pricePerDay;
    }

    public int getYearExperience() {
        return yearExperience;
    }

    public void setYearExperience(int yearExperience) {
        this.yearExperience = yearExperience;
    }

    public int getMonthExperience() {
        return monthExperience;
    }

    public void setMonthExperience(int monthExperience) {
        this.monthExperience = monthExperience;
    }

    public boolean isAvailable() {
        return available;
    }

    public void setAvailable(boolean available) {
        this.available = available;
    }

    public String toString(){
        return "NAME: " + this.name + "   PRICE PER DAY: " + String.valueOf(this.pricePerDay);
    }
}