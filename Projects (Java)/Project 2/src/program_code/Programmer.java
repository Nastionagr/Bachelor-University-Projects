package program_code;

public class Programmer extends Specialist {
    private String preferredType;

    public Programmer(String name, String education, String preferredType, String certificates, Float pricePerDay, int yearExperience, int monthExperience) {
        super(name, education, certificates, pricePerDay, yearExperience, monthExperience);
        this.preferredType = preferredType;
    }

    public String getPreferredType() {
        return preferredType;
    }

    public void setPreferredType(String preferredType) {
        this.preferredType = preferredType;
    }
}