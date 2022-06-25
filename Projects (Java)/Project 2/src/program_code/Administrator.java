package program_code;

public class Administrator extends Specialist {
    private String preferredType, preferredPlatform;

    public Administrator(String name, String education, String preferredType, String certificates, String preferredPlatform, Float pricePerDay, int yearExperience, int monthExperience){
        super(name, education, certificates, pricePerDay, yearExperience, monthExperience);
        this.preferredType = preferredType;
        this.preferredPlatform = preferredPlatform;
    }

    public String getPreferredType() {
        return preferredType;
    }

    public void setPreferredType(String preferredType) {
        this.preferredType = preferredType;
    }

    public String getPreferredPlatform() {
        return preferredPlatform;
    }

    public void setPreferredPlatform(String preferredPlatform) {
        this.preferredPlatform = preferredPlatform;
    }
}